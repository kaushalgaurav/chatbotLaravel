// src/FlowApp.jsx
import React, { useCallback, useState, useRef, useEffect, useMemo } from "react";
import {
  ReactFlow,
  Background,
  addEdge,
  useEdgesState,
  useNodesState,
  useReactFlow,
} from "@xyflow/react";
import "@xyflow/react/dist/style.css";

import { nodeTypes, initialNodes } from "../flowConfig";
import { Topbar, Toolbar, Chatbot, PopupMenu, NodeInspector, Toast } from "./index";
import usePublish from "../hooks/usePublish";

export default function FlowApp() {
  // -----------------------
  // Domain state
  // -----------------------
  const [nodes, setNodes, onNodesChange] = useNodesState(initialNodes);
  const [edges, setEdges, onEdgesChange] = useEdgesState([]);
  const [openChat, setOpenChat] = useState(false);
  const [popup, setPopup] = useState({ visible: false, x: 0, y: 0, sourceId: null });
  const [undoSnapshot, setUndoSnapshot] = useState(null);

  // inspector selected node id
  const [selectedNodeId, setSelectedNodeId] = useState(null);
  const selectedNode = nodes.find((n) => n.id === selectedNodeId) || null;

  const { zoomIn, zoomOut, fitView, toObject } = useReactFlow();

  // keep refs for potential external usage
  const nodesRef = useRef(nodes);
  const edgesRef = useRef(edges);
  useEffect(() => { nodesRef.current = nodes; edgesRef.current = edges; }, [nodes, edges]);
// add inside FlowApp component
useEffect(() => {
  try {
    localStorage.setItem("current-flow", JSON.stringify({ nodes, edges }));
  } catch (e) {
    // ignore quota errors
  }
}, [nodes, edges]);


  // ---------- usePublish hook ----------
  const getFlowSnapshot = useCallback(() => {
    try {
      const flow = toObject();
      if (!flow || (!flow.nodes && !flow.edges)) {
        return { nodes, edges };
      }
      return flow;
    } catch (e) {
      return { nodes, edges };
    }
  }, [toObject, nodes, edges]);

  const { publishing, toast, publish, clearToast } = usePublish(getFlowSnapshot);

  // -----------------------
  // delete / undo handlers (defined before nodesWithAdd)
  // -----------------------
  const deleteNode = useCallback((nodeId) => {
    // capture node + its edges for possible undo
    setNodes((nds) => {
      const removed = nds.find((n) => n.id === nodeId);
      if (removed) {
        const relatedEdges = edges.filter(e => e.source === nodeId || e.target === nodeId);
        setUndoSnapshot({ node: removed, edges: relatedEdges });
      }
      return nds.filter((n) => n.id !== nodeId);
    });

    setEdges((eds) => eds.filter((e) => e.source !== nodeId && e.target !== nodeId));
    // optional toast handled elsewhere
  }, [edges, setEdges, setNodes]);

  const undoDelete = useCallback(() => {
    if (!undoSnapshot) return;
    const { node, edges: removedEdges } = undoSnapshot;
    if (node) setNodes((nds) => [...nds, node]);
    if (removedEdges?.length) setEdges((eds) => [...eds, ...removedEdges]);
    setUndoSnapshot(null);
  }, [undoSnapshot, setNodes, setEdges]);

  // -----------------------
  // utility: default node data factory (restores condition/formula shapes)
  // -----------------------
  const getDefaultNodeData = useCallback((type, onAddClick) => {
    switch (type) {
      case "question":
        return { label: "Ask your question...", varName: "", onAddClick };
      case "buttons":
        return { question: "Choose an option:", options: ["Option 1", "Option 2"], varName: "", onAddClick };
      case "yesno":
        return { question: "Yes or No?", yesLabel: "Yes", noLabel: "No", varName: "", onAddClick };
      case "rating":
        return { question: "Rate from 1 to 5", onAddClick };
      case "message":
        return { text: "Bot message...", onAddClick };

      case "condition":
        return {
          logicType: "condition",
          conditions: [
            { id: `c-${Date.now()}-1`, label: "Yes", variable: "last_answer", operator: "==", value: "yes" },
            { id: `c-${Date.now()}-2`, label: "No", variable: "last_answer", operator: "==", value: "no" },
          ],
          defaultEdgeLabel: "default",
          onAddClick,
        };

      case "formula":
        return {
          logicType: "formula",
          formula: { expression: "parseFloat(num1) + parseFloat(num2)", outputVar: "sum" },
          onAddClick,
        };

      default:
        return { label: `${type} node`, onAddClick };
    }
  }, []);

  // -----------------------
  // popup add click injector
  // -----------------------
  const handleAddClick = useCallback((nodeId, e) => {
    const rect = e.currentTarget.getBoundingClientRect();
    setPopup({
      visible: true,
      x: rect.right + 8,
      y: rect.top,
      sourceId: nodeId,
    });
  }, []);

  // -----------------------
  // nodesWithAdd (memoized)
  // -----------------------
  const nodesWithAdd = useMemo(() => {
    return nodes.map((n) => ({
      ...n,
      data: {
        ...n.data,
        onAddClick: handleAddClick,
        onDelete: () => deleteNode(n.id),
      },
    }));
  }, [nodes, handleAddClick, deleteNode]);

  // -----------------------
  // connect handler
  // -----------------------
  const onConnect = useCallback((params) => {
    setEdges((eds) => addEdge({ ...params, animated: true }, eds));
  }, [setEdges]);

  // -----------------------
  // Export helper (kept)
  // -----------------------
  // const exportFlow = useCallback(() => {
  //   try {
  //     const flow = toObject();
  //     const blob = new Blob([JSON.stringify(flow, null, 2)], { type: "application/json" });
  //     const url = URL.createObjectURL(blob);
  //     const link = document.createElement("a");
  //     link.href = url;
  //     link.download = "flow.json";
  //     link.click();
  //   } catch (e) {
  //     console.error("Export failed", e);
  //   }
  // }, [toObject]);

  // -----------------------
  // handleSelectType uses factory to create correct node data
  // -----------------------
  const handleSelectType = useCallback((type) => {
    if (!popup.sourceId) return;
    const id = `${Date.now()}`;
    const defaultData = getDefaultNodeData(type, handleAddClick);

    const newNode = {
      id,
      type,
      position: { x: 400, y: 200 + nodes.length * 80 },
      data: defaultData,
    };

    setNodes((nds) => [...nds, newNode]);
    setEdges((eds) => [
      ...eds,
      { id: `e${popup.sourceId}-${id}`, source: popup.sourceId, target: id, animated: true },
    ]);

    setPopup({ visible: false, x: 0, y: 0, sourceId: null });
  }, [popup.sourceId, nodes.length, getDefaultNodeData, handleAddClick, setNodes, setEdges]);

  // -----------------------
  // Node update used by inspector
  // -----------------------
  const updateNode = useCallback((newNode) => {
    setNodes((nds) => nds.map((n) => (n.id === newNode.id ? newNode : n)));
  }, [setNodes]);

  // -----------------------
  // Render
  // -----------------------
  return (
    <div className="h-screen flex flex-col">
      {/* Topbar - pass publish handler from hook */}
      <Topbar onTest={() => setOpenChat(true)} onPublish={publish} publishing={publishing} />

      <div style={{ width: "100%", height: "calc(100vh - 56px)" }}>
        {/* Flow Builder */}
        <ReactFlow
          nodes={nodesWithAdd}
          edges={edges}
          onNodesChange={onNodesChange}
          onEdgesChange={onEdgesChange}
          onConnect={onConnect}
          nodeTypes={nodeTypes}
          // fitView 
          
           proOptions={{ hideAttribution: true }}
          onNodeDoubleClick={(evt, node) => setSelectedNodeId(node.id)}
        >
          <Background gap={20} color="gray" />
        </ReactFlow>

        {/* Toolbar */}
        <Toolbar
          onZoomIn={zoomIn}
          onZoomOut={zoomOut}
          onFitView={fitView}
          onUndo={() => alert("Undo placeholder")}
          onRedo={() => alert("Redo placeholder")}
        />

        {/* Export JSON */}
        {/* <div className="absolute bottom-5 left-5">
          <button onClick={exportFlow} className="bg-gray-700 text-white px-3 py-1 rounded">
            Export JSON
          </button>
        </div> */}

        {/* Popup Menu */}
        {popup.visible && (
          <PopupMenu
            x={popup.x}
            y={popup.y}
            onSelect={handleSelectType}
            onClose={() => setPopup({ visible: false, x: 0, y: 0, sourceId: null })}
          />
        )}

        {/* Node Inspector */}
        {selectedNode && (
          <NodeInspector node={selectedNode} onClose={() => setSelectedNodeId(null)} updateNode={updateNode} />
        )}

        {/* Chatbot Window */}
        {openChat && (
  <div
    className="position-fixed top-0 end-0 h-100 bg-white shadow"
    style={{ width: "24rem", zIndex: 1050, marginTop: "56px" }} // top offset for Topbar
  >
    <Chatbot
      nodes={nodes || []}
      edges={edges || []}
      onClose={() => setOpenChat(false)}
    />
  </div>
)}

      </div>

      {/* Publish / general toast (from your usePublish hook) */}
      <Toast toast={toast} onClear={clearToast} />
    </div>
  );
}
