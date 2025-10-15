import React from "react";
import { Handle, Position } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

/**
 * FormulaNode (Bootstrap 5)
 */
export default function FormulaNode({ id, data = {} }) {
  const formula = (data && data.formula) || { expression: "", outputVar: "" };
  const title = data.title || "Formula";

  return (
    <div
      className="position-relative bg-white rounded p-3 shadow"
      style={{
        width: "18rem", // Tailwind w-72 ~ 18rem
        borderLeft: "4px solid #6366f1", // indigo-500
      }}
    >
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />

      <div className="d-flex align-items-start justify-content-between mb-2">
        <div>
          <div className="fw-semibold small">{title}</div>
          <div className="small text-muted">formula</div>
        </div>
      </div>

      <div className="mt-1 small" style={{ color: "#374151" /* gray-700 */ }}>
        <div className="fw-medium small">Expression</div>
        <div className="mt-1 small text-dark text-break" style={{ wordBreak: "break-word" }}>
          {formula.expression ? (
            <code className="small text-break" style={{ wordBreak: "break-word" }}>
              {formula.expression}
            </code>
          ) : (
            <span className="text-muted">No expression — double click to edit</span>
          )}
        </div>

        <div className="mt-2 small text-muted">Output → {formula.outputVar || "(none)"}</div>
      </div>

     
      <div
        style={{ bottom: 10, left: "50%", transform: "translateX(-50%)" }}
        className="position-absolute small text-muted pointer-events-none"
      >
        out
      </div>

      {/* Incoming handle */}
      <Handle
        type="target"
        position={Position.Left}
        id="in"
        style={{ background: "#d1d5db" /* gray-300 */ }}
      />
    </div>
  );
}
