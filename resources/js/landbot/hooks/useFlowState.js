import { useCallback, useState, useRef } from "react";
import { useNodesState, useEdgesState, addEdge } from "@xyflow/react";

export default function useFlowState(initialNodes = [], initialEdges = []) {
  const [nodes, setNodes, onNodesChange] = useNodesState(initialNodes);
  const [edges, setEdges, onEdgesChange] = useEdgesState(initialEdges);

  const deleteNode = useCallback((nodeId) => {
    setNodes((nds) => nds.filter(n => n.id !== nodeId));
    setEdges((eds) => eds.filter(e => e.source !== nodeId && e.target !== nodeId));
  }, [setNodes, setEdges]);

  const addNode = useCallback((node, connectFromId = null) => {
    setNodes((nds) => [...nds, node]);
    if (connectFromId) {
      setEdges((eds) => [...eds, { id: `e${connectFromId}-${node.id}`, source: connectFromId, target: node.id, animated: true }]);
    }
  }, [setNodes, setEdges]);

  const onConnect = useCallback((params) => {
    setEdges((eds) => addEdge({ ...params, animated: true }, eds));
  }, [setEdges]);

  return {
    nodes, edges, onNodesChange, onEdgesChange, addNode, deleteNode, onConnect, setNodes, setEdges
  };
}
