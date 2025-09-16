// src/hooks/useChatbotEngine.js
import { useEffect, useRef, useState } from "react";
import { evaluateCondition, evaluateFormula, replaceTemplate } from "../utils/evaluate";

/**
 * useChatbotEngine
 * - nodes, edges: flow data
 * - startNodeId (optional) : start id; if not provided finds 'starting' type
 *
 * Returns:
 * { currentNode, currentNodeId, setCurrentNodeId, history, variables, handlers... }
 */
export default function useChatbotEngine({ nodes = [], edges = [], startNodeId = null }) {
  const safeNodes = Array.isArray(nodes) ? nodes : [];
  const safeEdges = Array.isArray(edges) ? edges : [];

  const startNode = safeNodes.find(n => n.type === "starting") || (startNodeId ? safeNodes.find(n => n.id === startNodeId) : null);
  const [currentNodeId, setCurrentNodeId] = useState(startNode?.id || null);

  const [history, setHistory] = useState([]);
  const [variables, setVariables] = useState({});
  const [inputValue, setInputValue] = useState("");

  const timeoutsRef = useRef([]);
  const currentNode = safeNodes.find(n => n.id === currentNodeId) || null;

  // clear timeouts on unmount or before rerun
  const clearScheduled = () => {
    timeoutsRef.current.forEach(t => clearTimeout(t));
    timeoutsRef.current = [];
  };
  const schedule = (fn, delay = 600) => {
    const t = setTimeout(fn, delay);
    timeoutsRef.current.push(t);
    return t;
  };

  // Helper to push bot or user messages to history
  const pushBot = (text) => {
    setHistory(h => {
      const last = h.at(-1);
      if (last?.sender === "bot" && last.text === text) return h;
      return [...h, { sender: "bot", text }];
    });
  };
  const pushUser = (text) => setHistory(h => [...h, { sender: "user", text }]);

  // Go to next node by matching outgoing edges (optionally by handle)
  const goToNext = (handleId = null) => {
    const nextEdge = safeEdges.find(e => e.source === currentNodeId && (handleId ? e.sourceHandle === handleId : true));
    if (nextEdge) setCurrentNodeId(nextEdge.target);
    else setCurrentNodeId(null);
  };

  // Store input and then navigate
  const submitInput = () => {
    if (!currentNode) return;
    if (!inputValue) return;
    pushUser(inputValue);
    const key = currentNode?.data?.varName || currentNode?.id || `answer_${Date.now()}`;
    setVariables(v => ({ ...v, [key]: inputValue }));
    setInputValue("");
    goToNext();
  };

  const clickButton = (option, handleId) => {
    pushUser(option);
    const key = currentNode?.data?.varName || currentNode?.id || `button_${Date.now()}`;
    setVariables(v => ({ ...v, [key]: option }));
    goToNext(handleId);
  };

  // Main effect: run node behavior whenever node changes
  useEffect(() => {
    clearScheduled();
    if (!currentNode) return;

    const data = currentNode.data || {};

    // Render bot message for most nodes.
    // For formula nodes we skip the pre-debug message — we'll push the result inside the formula handler.
    const shouldSkipPreMessage = currentNode.type === "formula" || (currentNode.type === "logic" && (currentNode.data || {}).logicType === "formula");

    const raw = (() => {
      switch (currentNode.type) {
        case "starting": return "👋 Hello! Let's start.";
        case "message": return currentNode.data?.text || "…";
        case "question": return currentNode.data?.label || "I have a question:";
        case "buttons": return currentNode.data?.question || "Please choose an option:";
        case "yesno": return currentNode.data?.question || "Make a choice:";
        case "rating": return currentNode.data?.question || "Rate from 1 to 5:";
        case "condition":
        case "logic": return "Evaluating conditions...";
        case "formula": return ""; // skip here, formula will push the result itself
        default: return "…";
      }
    })();

    if (!shouldSkipPreMessage && raw) {
      const templated = replaceTemplate(raw, variables);
      pushBot(templated);
    }


    // Node-specific actions
    // 1) Condition node
    if (currentNode.type === "condition" || (currentNode.type === "logic" && data.logicType === "condition")) {
      const conds = data.conditions || [];
      let matched = null;
      for (const c of conds) {
        if (evaluateCondition(c, variables)) { matched = c; break; }
      }

      // try edge by handle or label, fallback to default/out
      let edge = null;
      if (matched) {
        edge = safeEdges.find(e => e.source === currentNode.id && (e.sourceHandle === `out-${matched.id}` || (e.label && e.label === matched.label))) || null;
      }
      if (!edge) {
        edge = safeEdges.find(e => e.source === currentNode.id && (e.sourceHandle === "out-default" || (e.label && e.label === data.defaultEdgeLabel))) || safeEdges.find(e => e.source === currentNode.id) || null;
      }

      if (edge) schedule(() => setCurrentNodeId(edge.target), 350);
      else schedule(() => setCurrentNodeId(null), 350);
      return;
    }

    // 2) Formula node (robust)
    if (currentNode.type === "formula" || (currentNode.type === "logic" && data.logicType === "formula")) {
      const expr = data.formula?.expression || "";
      const outVar = data.formula?.outputVar || null;
      const result = evaluateFormula(expr, variables);
      const edge = safeEdges.find(e => e.source === currentNode.id) || null;

      // push a result message so user sees the computed value immediately
      const pretty = (v) => {
        if (v === undefined) return "undefined";
        if (v === null) return "null";
        if (typeof v === "object") {
          try { return JSON.stringify(v); } catch { return String(v); }
        }
        return String(v);
      };
      pushBot(outVar ? `✅ ${outVar} = ${pretty(result)}` : `✅ Result = ${pretty(result)}`);

      if (outVar) {
        // write variable only if changed; schedule navigation inside updater to avoid race
        setVariables(prev => {
          if (Object.prototype.hasOwnProperty.call(prev, outVar) && prev[outVar] === result) {
            // already same value -> just schedule navigation
            if (edge) schedule(() => setCurrentNodeId(edge.target), 350);
            else schedule(() => setCurrentNodeId(null), 350);
            return prev;
          }
          const newVars = { ...prev, [outVar]: result };
          if (edge) schedule(() => setCurrentNodeId(edge.target), 350);
          else schedule(() => setCurrentNodeId(null), 350);
          return newVars;
        });
      } else {
        if (edge) schedule(() => setCurrentNodeId(edge.target), 350);
        else schedule(() => setCurrentNodeId(null), 350);
      }

      return;
    }


    // 3) Automatic advance for starting & message nodes
    if (currentNode.type === "starting" || currentNode.type === "message") {
      const next = safeEdges.find(e => e.source === currentNode.id) || null;
      if (next) schedule(() => setCurrentNodeId(next.target), 700);
    }

    // questions / buttons / yesno / rating are user-driven — do not auto advance here

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [currentNodeId, nodes, edges, /* intentionally not including variables to avoid loops; we read variables snapshot above */]);

  // cleanup on unmount
  useEffect(() => clearScheduled, []);

  return {
    currentNode,
    currentNodeId,
    setCurrentNodeId,
    history,
    variables,
    inputValue,
    setInputValue,
    submitInput,
    clickButton,
    setVariables, // expose for tests or advanced flows
  };
}
