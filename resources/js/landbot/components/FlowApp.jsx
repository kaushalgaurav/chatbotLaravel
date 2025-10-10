import React, { useCallback, useState, useRef, useEffect, useMemo } from "react";
import { ReactFlow, Background, useReactFlow, useStore } from "@xyflow/react";
import "@xyflow/react/dist/style.css";

import useFlowState from "../hooks/useFlowState";
import { nodeTypes, initialNodes } from "../flowConfig";
import { Topbar, Toolbar, Chatbot, PopupMenu, NodeInspector, Toast } from "./index";
import usePublish from "../hooks/usePublish";
import AnimatedEdge from "../components/AnimatedEdge";

const PUBLISH_KEY = "published-flow:v1";
const BASE_URL = "http://127.0.0.1:8000";
const PUBLISH_API = "http://127.0.0.1:8000/chatbot/publish";
const AUTOSAVE_INTERVAL_MS = 10000;
const AUTOSAVE_ENABLED = true;

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
  const [popup, setPopup] = useState({ visible: false, x: 0, y: 0, sourceId: null, sourceHandle: null });
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
  const pauseAutosaveUntilNodeAddRef = useRef(false);
  const lastNodesCountRef = useRef(nodes.length || 0);

  // -----------------------
  // Helpers
  // -----------------------
  const loadFlowForBot = (chatbotId) => {
    const draftKey = `${PUBLISH_KEY}:${chatbotId}:draft`;
    const draft = localStorage.getItem(draftKey);
    if (draft) {
      try { return JSON.parse(draft); } catch { /* ignore */ }
    }
    const raw = localStorage.getItem(`${PUBLISH_KEY}:${String(chatbotId) || "anon"}`);
    if (raw) {
      try {
        const parsed = JSON.parse(raw);
        const latest = parsed.versions?.slice(-1)[0];
        return latest?.payload ?? { nodes: [], edges: [] };
      } catch { /* ignore */ }
    }
    return { nodes: initialNodes, edges: [] };
  };

 const getFlowSnapshot = useCallback(() => {
  try {
    const rf = toObject();
    // 🧠 Fallbacks: if edges missing, use local state or ReactFlow store directly
    const currentNodes = rf?.nodes?.length ? rf.nodes : nodes;
    const currentEdges =
      (rf?.edges?.length ? rf.edges : edges) ||
      useStore.getState?.()?.edges ||
      [];

    return { nodes: currentNodes, edges: currentEdges };
  } catch (e) {
    console.warn("[FlowApp] getFlowSnapshot fallback", e);
    return { nodes, edges };
  }
}, [toObject, nodes, edges]);

// call this to send to backend (isPublished = 0 for draft, 1 for publish)
const sendViaPublish = async (isPublished = false) => {
  try {
    const res = await publish({ is_published: isPublished ? 1 : 0, skipValidation: false, silent: false });
    if (res && res.ok) {
      console.log("✅ publish() succeeded", res);
    } else {
      console.warn("⚠ publish() returned:", res);
    }
  } catch (err) {
    console.error("Publish() error:", err);
  }
};

// example usage: sendViaPublish(false); // save draft
// sendViaPublish(true); // publish


  const { publishing, toast, publish, clearToast } = usePublish(getFlowSnapshot, {
    apiUrl: PUBLISH_API
  });

  // Autosave interval (unchanged)
  useEffect(() => {
    if (!PUBLISH_API || !AUTOSAVE_ENABLED) return;
    let mounted = true;
    const id = setInterval(() => {
      (async () => {
        if (!mounted) return;
        if (publishing) return;
        if (pauseAutosaveUntilNodeAddRef.current) return;
        try {
          await publish({ is_published: false, skipValidation: true, silent: true });
        } catch (e) {
          console.error("Autosave publish error", e);
        }
      })();
    }, AUTOSAVE_INTERVAL_MS);
    return () => { mounted = false; clearInterval(id); };
  }, [publish, publishing]);

  // Detect node additions to resume autosave
  useEffect(() => {
    const currentCount = nodes.length || 0;
    if (pauseAutosaveUntilNodeAddRef.current && currentCount > (lastNodesCountRef.current || 0)) {
      pauseAutosaveUntilNodeAddRef.current = false;
      lastNodesCountRef.current = currentCount;
      console.info("Autosave resumed: new node detected.");
    } else {
      lastNodesCountRef.current = currentCount;
    }
  }, [nodes]);

  // Node/edge handlers (unchanged)
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
      case "buttons": return { question: "Choose an option:", options: ["Option 1", "Option 2"], fallbackLabel: "Any of the above", varName: "", onAddClick };
      case "yesno": return { question: "Yes or No?", yesLabel: "Yes", noLabel: "No", varName: "", onAddClick };
      case "rating": return { question: "Rate from 1 to 5", onAddClick };
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

  const handleAddClick = useCallback((nodeId, maybeEventOrIndex) => {
    if (maybeEventOrIndex && maybeEventOrIndex.currentTarget) {
      try {
        const rect = maybeEventOrIndex.currentTarget.getBoundingClientRect();
        setPopup({ visible: true, x: rect.right + 8, y: rect.top, sourceId: nodeId, sourceHandle: "arrow" });
        return;
      } catch (err) {}
    }
    const optIndex = maybeEventOrIndex;
    const sourceHandle = optIndex === "fallback" ? "fallback" : (typeof optIndex !== "undefined" ? `option-${optIndex}` : "arrow");
    const centerX = Math.round(window.innerWidth / 2);
    const centerY = Math.round(window.innerHeight / 2);
    setPopup({ visible: true, x: centerX, y: centerY, sourceId: nodeId, sourceHandle });
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
    const sourceHandle = popup.sourceHandle || "arrow";
    setEdges(eds => [...eds, {
      id: `e${popup.sourceId}-${id}`,
      source: popup.sourceId,
      sourceHandle,
      target: id,
      targetHandle: "in",
      type: "animated"
    }]);
    setPopup({ visible: false, x: 0, y: 0, sourceId: null, sourceHandle: null });
  }, [popup.sourceId, popup.sourceHandle, nodes.length, getDefaultNodeData, handleAddClick, setNodes, setEdges]);

  const updateNode = useCallback((newNode) => {
    setNodes(nds => nds.map(n => n.id === newNode.id ? newNode : n));
  }, [setNodes]);

  // -----------------------
  // Load + auto-save draft (server-first + bot_id sync)
  // -----------------------
  useEffect(() => {
    const chatbotId = document.getElementById("root")?.dataset?.chatbotId ?? "";
    if (!chatbotId) return;

    // Respect botId from URL if present
    const urlParams = new URLSearchParams(window.location.search);
    const botIdFromUrl = urlParams.get("botId");
    const botIdKey = `bot_id:${chatbotId}`;

    const applyFlow = (flow) => {
      // Normalize backend / local flow shape
      if (!flow) {
        console.warn("[FlowApp] No flow to apply, loading defaults");
        setNodes(initialNodes);
        setEdges([]);
        return;
      }
      if (Array.isArray(flow)) flow = { nodes: flow, edges: [] };
      if (flow.payload && !flow.nodes) flow = flow.payload;
      if (Array.isArray(flow.payload)) flow = { nodes: flow.payload, edges: [] };
      if (flow.flow && !flow.nodes) flow = flow.flow;

      console.log("[FlowApp] ✅ Final normalized flow:", flow);

      const loadedNodes = flow?.nodes?.length ? flow.nodes : initialNodes;
      const patchedNodes = loadedNodes.map(n => {
        if (n.type === "buttons") {
          return {
            ...n,
            data: {
              ...n.data,
              fallbackLabel: n.data?.fallbackLabel ?? "Any of the above"
            }
          };
        }
        return n;
      });

      setNodes(patchedNodes);
      setEdges(flow?.edges || []);
    };

    (async () => {
      try {
        // STEP A: Ensure canonical bot_id in localStorage:
        // if botId is present in URL use it; otherwise try to get from server or existing localStorage
        let botId = localStorage.getItem(botIdKey) || null;
        if (botIdFromUrl && botIdFromUrl.startsWith("v-")) {
          botId = botIdFromUrl;
          localStorage.setItem(botIdKey, botId);
          console.info("[FlowApp] Using botId from URL:", botId);
        } else if (!botId) {
          // Try fetching server-side to retrieve an existing bot_id (publish-chatbot returns data which may include bot_id)
          try {
            const metaRes = await fetch(`${BASE_URL}/publish-chatbot/${encodeURIComponent(chatbotId)}`, {
              method: "GET",
              headers: { Accept: "application/json" },
              credentials: 'include'
            });
            if (metaRes.ok) {
              const metaJson = await metaRes.clone().json().catch(() => null);
              const backendBotId =
                metaJson?.data?.bot_id ||
                metaJson?.payload?.bot_id ||
                metaJson?.bot_id ||
                null;
              if (backendBotId) {
                botId = backendBotId;
                localStorage.setItem(botIdKey, botId);
                console.info("[FlowApp] Loaded existing botId from backend:", botId);
              }
            }
          } catch (err) {
            // ignore meta fetch errors, we'll try to load flow normally below
            console.warn("[FlowApp] bot_id fetch attempt failed:", err);
          }
        } else {
          console.info("[FlowApp] Loaded existing botId from localStorage:", botId);
        }

        // STEP B: Fetch flow from backend (server-first)
        // First try the authenticated numeric endpoint (publish-chatbot/:chatbotId) — this can also include payload
        try {
          console.log("[FlowApp] Fetching flow from backend for chatbotId:", chatbotId);
          const res = await fetch(`${BASE_URL}/publish-chatbot/${encodeURIComponent(chatbotId)}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
            credentials: 'include'
          });

          if (res.ok) {
            const json = await res.json().catch(() => null);
            const flowFromServer =
              json?.data?.payload?.flow ||
              json?.data?.payload ||
              json?.payload?.flow ||
              json?.payload ||
              json?.flow ||
              json?.data ||
              null;

            // if server provided a bot_id here, persist it (keeps incognito in sync)
            const maybeBotId =
              json?.data?.bot_id ||
              json?.payload?.bot_id ||
              json?.bot_id ||
              null;
            if (maybeBotId) {
              localStorage.setItem(botIdKey, maybeBotId);
              console.info("[FlowApp] Persisted backend bot_id:", maybeBotId);
            }

            if (flowFromServer) {
              console.log("[FlowApp] ✅ Loaded flow from backend");
              applyFlow(flowFromServer);
              try { localStorage.setItem(`${PUBLISH_KEY}:${chatbotId}:draft`, JSON.stringify(flowFromServer)); } catch (e) {}
              return;
            } else {
              console.info("[FlowApp] Backend responded OK but no flow payload");
            }
          } else {
            console.info("[FlowApp] publish-chatbot status:", res.status);
          }
        } catch (err) {
          console.warn('[FlowApp] Could not fetch published flow from server', err);
        }

        // If we have a canonical botId (from URL or persisted), try public published/:botId endpoint
        const persistedBotId = localStorage.getItem(botIdKey);
        if (persistedBotId) {
          try {
            const pubRes = await fetch(`${BASE_URL}/published/${encodeURIComponent(persistedBotId)}`, {
              method: "GET",
              headers: { Accept: "application/json" }
            });
            if (pubRes.ok) {
              const pubJson = await pubRes.json().catch(() => null);
              const flowFromPub = pubJson?.payload?.flow || pubJson?.payload || pubJson?.flow || pubJson || null;
              if (flowFromPub) {
                console.info("[FlowApp] ✅ Loaded flow from published/:botId");
                applyFlow(flowFromPub);
                try { localStorage.setItem(`${PUBLISH_KEY}:${chatbotId}:draft`, JSON.stringify(flowFromPub)); } catch (e) {}
                return;
              }
            } else {
              console.info("[FlowApp] published/:botId status:", pubRes.status);
            }
          } catch (err) {
            console.warn("[FlowApp] fetching published/:botId failed:", err);
          }
        }

        // STEP C: fallback to local draft
        try {
          const draftKey = `${PUBLISH_KEY}:${chatbotId}:draft`;
          const draftRaw = localStorage.getItem(draftKey);
          if (draftRaw) {
            console.log("[FlowApp] Loaded flow from localStorage draft");
            applyFlow(JSON.parse(draftRaw));
            return;
          }
        } catch (err) {
          console.warn("[FlowApp] Local draft load failed:", err);
        }

        // STEP D: initial nodes
        console.log("[FlowApp] No flow found; using initialNodes");
        applyFlow({ nodes: initialNodes, edges: [] });
      } catch (err) {
        console.warn("[FlowApp] Unexpected error in flow load:", err);
        applyFlow({ nodes: initialNodes, edges: [] });
      }
    })();

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [setNodes, setEdges]);

  // Persist draft whenever nodes/edges change
  useEffect(() => {
    const chatbotId = document.getElementById("root")?.dataset?.chatbotId ?? "";
    if (!chatbotId) return;
    const draftKey = `${PUBLISH_KEY}:${chatbotId}:draft`;
    try { localStorage.setItem(draftKey, JSON.stringify({ nodes, edges })); } catch (err) { console.warn("[FlowApp] failed to persist draft:", err); }
  }, [nodes, edges]);

  // Manual publish handler
  const handlePublish = useCallback(async () => {
    try {
      setManualPublishing(true);
      const res = await publish({ is_published: true, skipValidation: false });
      if (res && res.ok) {
        pauseAutosaveUntilNodeAddRef.current = true;
        lastNodesCountRef.current = nodes.length || 0;
        console.info("Publish succeeded — autosave paused until a new node is added.");
      } else {
        console.warn("Publish returned non-ok result", res);
      }
    } catch (err) {
      console.error("Publish error", err);
    } finally {
      setManualPublishing(false);
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
          maxZoom={1}
        >
          <Background gap={50} color="rgba(255,255,255,0.1)" variant="lines" />
        </ReactFlow>

        <Toolbar zoom={zoom} onZoomIn={zoomIn} onZoomOut={zoomOut} onFitView={fitView}
          onUndo={() => undoDelete()} onRedo={() => alert("Redo placeholder")} />

        {popup.visible && <PopupMenu x={popup.x} y={popup.y} onSelect={handleSelectType} onClose={() => setPopup({ visible: false, x: 0, y: 0, sourceId: null, sourceHandle: null })} />}
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
