// resources/js/landbot/components/FlowApp.jsx
import React, { useCallback, useState, useRef, useEffect, useMemo } from "react";
import { ReactFlow, Background, useReactFlow, useStore } from "@xyflow/react";
import "@xyflow/react/dist/style.css";

import useFlowState from "../hooks/useFlowState";
import { nodeTypes, initialNodes } from "../flowConfig";
import { Topbar, Toolbar, Chatbot, PopupMenu, NodeInspector, Toast } from "./index";
import usePublish from "../hooks/usePublish";
import AnimatedEdge from "../components/AnimatedEdge";

const PUBLISH_KEY = "published-flow:v1";
const PUBLISH_API = "http://127.0.0.1:8000/chatbot/publish";
const AUTOSAVE_INTERVAL_MS = 10000; 

export default function FlowApp() {
  // -----------------------
  // Flow state
  // -----------------------
  const {
    nodes, edges, setNodes, setEdges,
    onNodesChange, onEdgesChange,
    onConnect
  } = useFlowState(initialNodes, []);

  const [openChat, setOpenChat] = useState(false);
  const [popup, setPopup] = useState({ visible: false, x: 0, y: 0, sourceId: null });
  const [undoSnapshot, setUndoSnapshot] = useState(null);
  const [selectedNodeId, setSelectedNodeId] = useState(null);
   const [manualPublishing, setManualPublishing] = useState(false);
  const selectedNode = nodes.find(n => n.id === selectedNodeId) || null;
  const { zoomIn, zoomOut, fitView, toObject } = useReactFlow();
  const zoom = useStore(s => s.transform[2]);
  const edgeTypes = useMemo(() => ({ animated: AnimatedEdge }), []);
  

  // -----------------------
  // Autosave control refs (NEW)
  // -----------------------
  const pauseAutosaveUntilNodeAddRef = useRef(false); // when true, autosave is paused until new node added
  const lastNodesCountRef = useRef(nodes.length || 0); // baseline to detect new node additions

  // -----------------------
  // Helpers
  // -----------------------
  const loadFlowForBot = (chatbotId) => {
    const draftKey = `${PUBLISH_KEY}:${chatbotId}:draft`;
    const draft = localStorage.getItem(draftKey);

    if (draft) {
      try {
        return JSON.parse(draft);
      } catch {}
    }

    const raw = localStorage.getItem(`${PUBLISH_KEY}:${String(chatbotId) || "anon"}`);
    if (raw) {
      try {
        const parsed = JSON.parse(raw);
        const latest = parsed.versions?.slice(-1)[0];
        return latest?.payload ?? { nodes: [], edges: [] };
      } catch {}
    }
    return { nodes: initialNodes, edges: [] };
  };

  const getFlowSnapshot = useCallback(() => {
    try {
      const flow = toObject();
      return flow?.nodes || flow?.edges ? flow : { nodes, edges };
    } catch {
      return { nodes, edges };
    }
  }, [toObject, nodes, edges]);

  const { publishing, toast, publish, clearToast } = usePublish(getFlowSnapshot, {
    apiUrl: PUBLISH_API
  });

  // -----------------------
  // Autosave every 10s (to publish API) — saves draft with is_published: false (silent)
  // Paused after manual publish until a new node is added
  // -----------------------
  useEffect(() => {
    if (!PUBLISH_API) return;

    let mounted = true;
    const id = setInterval(() => {
      (async () => {
        if (!mounted) return;
        if (publishing) return;
        if (pauseAutosaveUntilNodeAddRef.current) return; // PAUSED after manual publish until node add
        try {
          await publish({ is_published: false, skipValidation: true, silent: true });
        } catch (e) {
          console.error("Autosave publish error", e);
        }
      })();
    }, AUTOSAVE_INTERVAL_MS);

    return () => {
      mounted = false;
      clearInterval(id);
    };
  }, [publish, publishing]);

  // -----------------------
  // Detect node additions and resume autosave if paused
  // -----------------------
  useEffect(() => {
    const currentCount = nodes.length || 0;

    // If autosave is paused waiting for node-add and a node is added -> resume
    if (pauseAutosaveUntilNodeAddRef.current && currentCount > (lastNodesCountRef.current || 0)) {
      pauseAutosaveUntilNodeAddRef.current = false;
      lastNodesCountRef.current = currentCount;
      console.info("Autosave resumed: new node detected.");
    } else {
      // keep baseline up to date
      lastNodesCountRef.current = currentCount;
    }
  }, [nodes]);

  // -----------------------
  // Node / Edge handlers
  // -----------------------
  const deleteNodeHandler = useCallback((nodeId) => {
    setNodes(nds => {
      const removed = nds.find(n => n.id === nodeId);
      if (removed) {
        const relatedEdges = edges.filter(e => e.source === nodeId || e.target === nodeId);
        setUndoSnapshot({ node: removed, edges: relatedEdges });
      }
      return nds.filter(n => n.id !== nodeId);
    });
    setEdges(eds => eds.filter(e => e.source !== nodeId && e.target !== nodeId));
  }, [edges, setEdges, setNodes]);

  const undoDelete = useCallback(() => {
    if (!undoSnapshot) return;
    const { node, edges: removedEdges } = undoSnapshot;
    if (node) setNodes(nds => [...nds, node]);
    if (removedEdges?.length) setEdges(eds => [...eds, ...removedEdges]);
    setUndoSnapshot(null);
  }, [undoSnapshot, setNodes, setEdges]);

  const getDefaultNodeData = useCallback((type, onAddClick) => {
    switch (type) {
      case "question": return { label: "Ask your question...", varName: "", onAddClick };
      case "buttons": return { question: "Choose an option:", options: ["Option 1", "Option 2"], varName: "", onAddClick };
      case "yesno":   return { question: "Yes or No?", yesLabel: "Yes", noLabel: "No", varName: "", onAddClick };
      case "rating":  return { question: "Rate from 1 to 5", onAddClick };
      case "message": return { text: "Bot message...", onAddClick };
      case "condition": return {
        logicType: "condition",
        conditions: [
          { id: `c-${Date.now()}-1`, label: "Yes", variable: "last_answer", operator: "==", value: "yes" },
          { id: `c-${Date.now()}-2`, label: "No", variable: "last_answer", operator: "==", value: "no" },
        ],
        defaultEdgeLabel: "default", onAddClick
      };
      case "formula": return { logicType: "formula", formula: { expression: "parseFloat(num1) + parseFloat(num2)", outputVar: "sum" }, onAddClick };
      default: return { label: `${type} node`, onAddClick };
    }
  }, []);

  const handleAddClick = useCallback((nodeId, e) => {
    const rect = e.currentTarget.getBoundingClientRect();
    setPopup({ visible: true, x: rect.right + 8, y: rect.top, sourceId: nodeId });
  }, []);

  const nodesWithAdd = useMemo(() => nodes.map(n => ({
    ...n,
    data: { ...n.data, onAddClick: handleAddClick, onDelete: () => deleteNodeHandler(n.id) }
  })), [nodes, handleAddClick, deleteNodeHandler]);

  const handleConnect = useCallback((params) => {
    onConnect({
      ...params,
      sourceHandle: params.sourceHandle || "arrow",
      targetHandle: params.targetHandle || "in",
      type: params.type || "animated"
    });
  }, [onConnect]);

  const handleSelectType = useCallback((type) => {
    if (!popup.sourceId) return;
    const id = `${Date.now()}`;
    const newNode = {
      id, type, data: getDefaultNodeData(type, handleAddClick),
      position: { x: 400, y: 200 + nodes.length * 80 }
    };
    setNodes(nds => [...nds, newNode]);
    setEdges(eds => [...eds, { id: `e${popup.sourceId}-${id}`, source: popup.sourceId, sourceHandle: "arrow", target: id, targetHandle: "in", type: "animated" }]);
    setPopup({ visible: false, x: 0, y: 0, sourceId: null });
  }, [popup.sourceId, nodes.length, getDefaultNodeData, handleAddClick, setNodes, setEdges]);

  const updateNode = useCallback((newNode) => {
    setNodes(nds => nds.map(n => n.id === newNode.id ? newNode : n));
  }, [setNodes]);

  // -----------------------
  // Load + auto-save draft
  // -----------------------
  useEffect(() => {
    const chatbotId = document.getElementById("root")?.dataset?.chatbotId ?? "";
    if (!chatbotId) return;
    const flow = loadFlowForBot(chatbotId);
    setNodes(flow?.nodes?.length ? flow.nodes : initialNodes);
    setEdges(flow?.edges || []);
  }, [setNodes, setEdges]);

  useEffect(() => {
    const chatbotId = document.getElementById("root")?.dataset?.chatbotId ?? "";
    if (!chatbotId) return;
    const draftKey = `${PUBLISH_KEY}:${chatbotId}:draft`;
    localStorage.setItem(draftKey, JSON.stringify({ nodes, edges }));
  }, [nodes, edges]);

  // -----------------------
  // Manual publish handler (PAUSES autosave until new node added)
  // -----------------------
   // -----------------------
  // Manual publish handler (PAUSES autosave until new node added)
  // Also track manualPublishing to avoid showing publish spinner for autosave
  // -----------------------
  const handlePublish = useCallback(async () => {
    try {
      setManualPublishing(true); // start manual spinner
      const res = await publish({ is_published: true, skipValidation: false });
      // res.ok true means publish succeeded according to usePublish
      if (res && res.ok) {
        // pause autosave until user adds a new node
        pauseAutosaveUntilNodeAddRef.current = true;
        // set baseline node count
        lastNodesCountRef.current = nodes.length || 0;
        console.info("Publish succeeded — autosave paused until a new node is added.");
      } else {
        // optional: handle non-ok result (toast already shown by usePublish)
        console.warn("Publish returned non-ok result", res);
      }
    } catch (err) {
      console.error("Publish error", err);
    } finally {
      setManualPublishing(false); // stop manual spinner
    }
  }, [publish, nodes.length]);


  // -----------------------
  // Render
  // -----------------------
  return (
    <div className="h-screen flex flex-col">
      <Topbar onTest={() => setOpenChat(true)} onPublish={handlePublish} publishing={manualPublishing} />

      <div style={{ width: "100%", height: "calc(100vh - 56px)" }}>
        <ReactFlow
          nodes={nodesWithAdd} edges={edges}
          onNodesChange={onNodesChange} onEdgesChange={onEdgesChange}
          onConnect={handleConnect} nodeTypes={nodeTypes} edgeTypes={edgeTypes}
          proOptions={{ hideAttribution: true }}
          onNodeDoubleClick={(_, node) => setSelectedNodeId(node.id)}
          fitView style={{ backgroundColor: "#454B6B" }}
        >
          <Background gap={100} color="rgba(255,255,255,0.1)" variant="lines" />
        </ReactFlow>

        <Toolbar zoom={zoom} onZoomIn={zoomIn} onZoomOut={zoomOut} onFitView={fitView}
                 onUndo={() => undoDelete()} onRedo={() => alert("Redo placeholder")} />

        {popup.visible && <PopupMenu x={popup.x} y={popup.y} onSelect={handleSelectType} onClose={() => setPopup({ visible: false, x: 0, y: 0, sourceId: null })} />}
        {selectedNode && <NodeInspector node={selectedNode} onClose={() => setSelectedNodeId(null)} updateNode={updateNode} />}
        {openChat && (
          <div className="position-fixed top-0 end-0 h-100 bg-white shadow"
               style={{ width: "24rem", zIndex: 1050, marginTop: "56px" }}>
            <Chatbot nodes={nodes} edges={edges} onClose={() => setOpenChat(false)} />
          </div>
        )}
      </div>

      <Toast toast={toast} onClear={clearToast} />
    </div>
  );
}
