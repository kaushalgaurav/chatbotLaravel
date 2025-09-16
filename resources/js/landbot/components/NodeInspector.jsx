import React from "react";
import { useReactFlow } from "@xyflow/react";
import ConditionEditor from "./nodes/ConditionEditor";
import FormulaEditor from "./nodes/FormulaEditor";

export default function NodeInspector({ node, onClose, updateNode: updateNodeProp }) {
  const { setNodes } = useReactFlow();

  // allow parent to pass updateNode (safer when NodeInspector is rendered outside ReactFlow)
  const updateNode =
    updateNodeProp ||
    ((newNode) => {
      setNodes((nds) => nds.map((n) => (n.id === newNode.id ? newNode : n)));
    });

  if (!node) return null;

  return (
    <div className="fixed right-0 top-16 w-96 h-[calc(100vh-4rem)] bg-white shadow-lg z-50 overflow-auto">
      <div className="p-3 border-b flex justify-between">
        <div>
          <div className="font-semibold">{node.type} node</div>
          <div className="text-xs text-gray-500">id: {node.id}</div>
        </div>
        <div>
          <button onClick={onClose} className="text-sm px-2 py-1 border rounded">
            Close
          </button>
        </div>
      </div>

      <div className="p-3">
        {node.type === "message" && (
          <>
            <label className="text-xs text-gray-600">Message</label>
            <textarea
              className="w-full p-2 border rounded"
              value={node.data?.text || ""}
              onChange={(e) =>
                updateNode({ ...node, data: { ...node.data, text: e.target.value } })
              }
            />
          </>
        )}

        {node.type === "question" && (
          <>
            <label className="text-xs text-gray-600">Question</label>
            <input
              className="w-full p-2 border rounded"
              value={node.data?.label || ""}
              onChange={(e) =>
                updateNode({ ...node, data: { ...node.data, label: e.target.value } })
              }
            />
            <label className="text-xs text-gray-600 mt-2">Variable name</label>
            <input
              className="w-full p-2 border rounded"
              value={node.data?.varName || ""}
              onChange={(e) =>
                updateNode({ ...node, data: { ...node.data, varName: e.target.value } })
              }
            />
          </>
        )}

        {/* New: handle separate condition/formula node types, plus legacy "logic" */}
        {(node.type === "condition" ||
          (node.type === "logic" && node.data?.logicType === "condition")) && (
          <ConditionEditor node={node} updateNode={updateNode} />
        )}

        {(node.type === "formula" ||
          (node.type === "logic" && node.data?.logicType === "formula")) && (
          <FormulaEditor node={node} updateNode={updateNode} />
        )}
      </div>
    </div>
  );
}
