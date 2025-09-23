// src/components/NodeInspector.jsx
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
    <div
      className="position-fixed bg-white shadow overflow-auto"
      style={{
        right: 0,
        top: "4rem", // equivalent to top-16 in Tailwind (4rem)
        width: "24rem", // approx w-96 (24rem)
        height: "calc(100vh - 4rem)",
        zIndex: 50,
      }}
    >
      <div className="p-3 border-bottom d-flex justify-content-between align-items-start">
        <div>
          <div className="fw-semibold">{node.type} node</div>
          <div className="small text-muted">id: {node.id}</div>
        </div>
        <div>
          <button onClick={onClose} className="btn btn-outline-secondary btn-sm">
            Close
          </button>
        </div>
      </div>

      <div className="p-3">
        {node.type === "message" && (
          <>
            <label className="form-label small text-muted">Message</label>
            <textarea
              className="form-control"
              value={node.data?.text || ""}
              onChange={(e) =>
                updateNode({ ...node, data: { ...node.data, text: e.target.value } })
              }
              rows={4}
            />
          </>
        )}

        {node.type === "question" && (
          <>
            <label className="form-label small text-muted">Question</label>
            <input
              className="form-control mb-2"
              value={node.data?.label || ""}
              onChange={(e) =>
                updateNode({ ...node, data: { ...node.data, label: e.target.value } })
              }
            />
            <label className="form-label small text-muted">Variable name</label>
            <input
              className="form-control"
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
