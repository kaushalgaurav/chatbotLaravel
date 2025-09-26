// resources/js/landbot/components/FlowApp.jsx
import React, { useCallback, useState, useRef, useEffect, useMemo } from "react";
import {
  ReactFlow,
  Background,
  useReactFlow,
  useStore,
  MiniMap
} from "@xyflow/react";
import "@xyflow/react/dist/style.css";

import useFlowState from "../hooks/useFlowState";
import { nodeTypes, initialNodes } from "../flowConfig";
import { Topbar, Toolbar, Chatbot, PopupMenu, NodeInspector, Toast } from "./index";
import usePublish from "../hooks/usePublish";
import AnimatedEdge from "../components/AnimatedEdge";

export default function FlowApp() {
  // -----------------------
  // Domain state (hook handles loading + saving)
  // -----------------------
  const {
    nodes,
    edges,
    setNodes,
    setEdges,
    onNodesChange,
    onEdgesChange,
    addNode,
    deleteNode,
    onConnect,
    resetFlow,
  } = useFlowState(initialNodes, []);

  const [openChat, setOpenChat] = useState(false);
  const [popup, setPopup] = useState({ visible: false, x: 0, y: 0, sourceId: null });
  const [undoSnapshot, setUndoSnapshot] = useState(null);

  // inspector selected node id
  const [selectedNodeId, setSelectedNodeId] = useState(null);
  const selectedNode = nodes.find((n) => n.id === selectedNodeId) || null;

  const { zoomIn, zoomOut, fitView, toObject } = useReactFlow();
  const zoom = useStore((state) => state.transform[2]);
  // keep refs for potential external usage
  const nodesRef = useRef(nodes);
  const edgesRef = useRef(edges);
  useEffect(() => { nodesRef.current = nodes; edgesRef.current = edges; }, [nodes, edges]);
  const edgeTypes = React.useMemo(() => ({ animated: AnimatedEdge }), []);


  // small debug so you can verify nodes/edges reached the component
  useEffect(() => {
    console.debug("[FlowApp] nodes count:", nodes?.length ?? 0, "edges count:", edges?.length ?? 0);
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
  // delete / undo handlers
  // -----------------------
  const deleteNodeHandler = useCallback((nodeId) => {
    setNodes((nds) => {
      const removed = nds.find((n) => n.id === nodeId);
      if (removed) {
        const relatedEdges = edges.filter(e => e.source === nodeId || e.target === nodeId);
        setUndoSnapshot({ node: removed, edges: relatedEdges });
      }
      return nds.filter((n) => n.id !== nodeId);
    });

    setEdges((eds) => eds.filter((e) => e.source !== nodeId && e.target !== nodeId));
  }, [edges, setEdges, setNodes]);

  const undoDelete = useCallback(() => {
    if (!undoSnapshot) return;
    const { node, edges: removedEdges } = undoSnapshot;
    if (node) setNodes((nds) => [...nds, node]);
    if (removedEdges?.length) setEdges((eds) => [...eds, ...removedEdges]);
    setUndoSnapshot(null);
  }, [undoSnapshot, setNodes, setEdges]);

  // -----------------------
  // node data factory
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
  // nodesWithAdd
  // -----------------------
  const nodesWithAdd = useMemo(() => {
    return nodes.map((n) => ({
      ...n,
      data: {
        ...n.data,
        onAddClick: handleAddClick,
        onDelete: () => deleteNodeHandler(n.id),
      },
    }));
  }, [nodes, handleAddClick, deleteNodeHandler]);

  // -----------------------
  // connect handler (ensure handles & edge type are present)
  // -----------------------
  const handleConnect = useCallback((params) => {
    const normalized = {
      ...params,
      // keep whatever the UI supplied but fall back to our handles/type
      sourceHandle: params.sourceHandle || "arrow",
      targetHandle: params.targetHandle || "in",
      type: params.type || "animated",
    };
    // call hook's onConnect so its existing logic still runs
    onConnect(normalized);
  }, [onConnect]);

  // -----------------------
  // handleSelectType
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
      {
        id: `e${popup.sourceId}-${id}`,
        source: popup.sourceId,
        sourceHandle: "arrow",    // attach to arrow on source
        target: id,
        targetHandle: "in",       // attach to left handle on target
        type: "animated",         // use animated/custom edge rendering
      },
    ]);

    setPopup({ visible: false, x: 0, y: 0, sourceId: null });
  }, [popup.sourceId, nodes.length, getDefaultNodeData, handleAddClick, setNodes, setEdges]);

  // -----------------------
  // updateNode (inspector)
  // -----------------------
  const updateNode = useCallback((newNode) => {
    setNodes((nds) => nds.map((n) => (n.id === newNode.id ? newNode : n)));
  }, [setNodes]);

  // -----------------------
  // Render
  // -----------------------
  return (
    <div className="h-screen flex flex-col">
      <Topbar onTest={() => setOpenChat(true)} onPublish={publish} publishing={publishing} />

      <div style={{ width: "100%", height: "calc(100vh - 56px)" }}>
        <ReactFlow
          nodes={nodesWithAdd}
          edges={edges}
          onNodesChange={onNodesChange}
          onEdgesChange={onEdgesChange}
          onConnect={handleConnect}
          nodeTypes={nodeTypes}
          edgeTypes={edgeTypes}
          proOptions={{ hideAttribution: true }}
          onNodeDoubleClick={(evt, node) => setSelectedNodeId(node.id)}
          fitView 
          style={{ backgroundColor: "#454B6B" }}
        >
          <Background gap={60} color="rgba(255,255,255,0.1)"  variant="lines"  />
        </ReactFlow>

        <Toolbar
          zoom={zoom}
          onZoomIn={zoomIn}
          onZoomOut={zoomOut}
          onFitView={fitView}
          onUndo={() => alert("Undo placeholder")}
          onRedo={() => alert("Redo placeholder")}
        />

        {popup.visible && (
          <PopupMenu
            x={popup.x}
            y={popup.y}
            onSelect={handleSelectType}
            onClose={() => setPopup({ visible: false, x: 0, y: 0, sourceId: null })}
          />
        )}

        {selectedNode && (
          <NodeInspector node={selectedNode} onClose={() => setSelectedNodeId(null)} updateNode={updateNode} />
        )}

        {openChat && (
          <div
            className="position-fixed top-0 end-0 h-100 bg-white shadow"
            style={{ width: "24rem", zIndex: 1050, marginTop: "56px" }}
          >
            <Chatbot
              nodes={nodes || []}
              edges={edges || []}
              onClose={() => setOpenChat(false)}
            />
          </div>
        )}
      </div>

      <Toast toast={toast} onClear={clearToast} />
    </div>
  );
} 
