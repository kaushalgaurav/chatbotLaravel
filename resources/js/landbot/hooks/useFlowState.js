// resources/js/landbot/hooks/useFlowState.js
import { useCallback, useRef, useEffect } from "react";
import { useNodesState, useEdgesState, addEdge } from "@xyflow/react";

const STORAGE_KEYS = ["landbot-flow-v1", "current-flow"]; // try both; keeps backwards compatibility
const SAVE_DEBOUNCE_MS = 250;


function safeParse(raw) {
  try {
    return JSON.parse(raw);
  } catch (e) {
    return null;
  }
}

export default function useFlowState(initialNodes = [], initialEdges = []) {
  // Try to read saved data (check multiple keys)
  let saved = null;
  try {
    for (const key of STORAGE_KEYS) {
      const raw = localStorage.getItem(key);
      if (raw) {
        const parsed = safeParse(raw);
        if (parsed && (Array.isArray(parsed.nodes) || Array.isArray(parsed.edges))) {
          saved = parsed;
          // keep using the first key that contains valid content
          break;
        }
      }
    }
  } catch (e) {
    // ignore storage errors
    saved = null;
  }

  const startNodes = (saved && Array.isArray(saved.nodes)) ? saved.nodes : initialNodes;
  const startEdges = (saved && Array.isArray(saved.edges)) ? saved.edges : initialEdges;

  const [nodes, setNodes, onNodesChange] = useNodesState(startNodes);
  const [edges, setEdges, onEdgesChange] = useEdgesState(startEdges);

  // debug: let us know what was loaded (will show in console)
  useEffect(() => {
    if (saved) {
      console.debug("[useFlowState] loaded saved flow:", {
        nodes: saved.nodes?.length ?? 0,
        edges: saved.edges?.length ?? 0,
      });
    } else {
      console.debug("[useFlowState] no saved flow found; using initialNodes");
    }
    // we only want this to run once on mount
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  // debounce persistence
  const saveTimeout = useRef(null);
  const persist = useCallback((n, e) => {
    try {
      // Always write to the canonical key 'current-flow' so DevTools matches
      localStorage.setItem("current-flow", JSON.stringify({ nodes: n, edges: e }));
      // Also keep legacy key so other code sees it if needed
      try {
        localStorage.setItem("landbot-flow-v1", JSON.stringify({ nodes: n, edges: e }));
      } catch (_) {}
    } catch (err) {
      console.warn("[useFlowState] failed to persist flow", err);
    }
  }, []);

  useEffect(() => {
    if (saveTimeout.current) clearTimeout(saveTimeout.current);
    saveTimeout.current = setTimeout(() => persist(nodes, edges), SAVE_DEBOUNCE_MS);

    return () => {
      if (saveTimeout.current) {
        clearTimeout(saveTimeout.current);
        saveTimeout.current = null;
      }
    };
  }, [nodes, edges, persist]);

  const deleteNode = useCallback((nodeId) => {
    setNodes((nds) => nds.filter((n) => n.id !== nodeId));
    setEdges((eds) => eds.filter((e) => e.source !== nodeId && e.target !== nodeId));
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

  const resetFlow = useCallback(() => {
    setNodes(initialNodes);
    setEdges(initialEdges);
    try {
      localStorage.removeItem("current-flow");
      localStorage.removeItem("landbot-flow-v1");
    } catch (_) {}
  }, [setNodes, setEdges, initialNodes, initialEdges]);

  return {
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
  };
}