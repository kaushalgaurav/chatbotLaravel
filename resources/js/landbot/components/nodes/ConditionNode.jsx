import React from "react";
import { Handle, Position } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

/**
 * ConditionNode (Bootstrap 5)
 */
export default function ConditionNode({ id, data = {} }) {
  const conditions = Array.isArray(data.conditions) ? data.conditions : [];
  const title = data.title || "Condition";
  const defaultLabel = data.defaultEdgeLabel || "default";

  return (
    <div
      className="position-relative bg-white rounded p-3 shadow"
      style={{
        width: "18rem", // Tailwind w-72 ~ 18rem
        borderLeft: "4px solid #db2777", // pink-500
      }}
    >
      {/* Add the kebab menu */}
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />

      <div className="d-flex align-items-start justify-content-between mb-2">
        <div>
          <div className="fw-semibold small">{title}</div>
          <div className="small text-muted">condition</div>
        </div>
      </div>

      <div className="mt-1 small" style={{ color: "#374151" /* gray-700 */ }}>
        <div className="fw-medium small">Conditions</div>

        {conditions.length === 0 && (
          <div className="mt-2 small text-muted">No conditions configured — double click to edit.</div>
        )}

        {conditions.map((c, i) => (
          <div
            key={c.id || i}
            className="mt-2 p-2 bg-light rounded small border"
          >
            <div className="fw-medium">{c.label || `Cond ${i + 1}`}</div>
            <div className="small text-secondary">
              {c.variable || "<variable>"} {c.operator || "=="} {String(c.value ?? "<value>")}
            </div>
          </div>
        ))}

        <div className="mt-3 small text-muted">Default → {defaultLabel}</div>
      </div>

      {/* Source handles for each condition (aligned vertically on the right) */}
      {conditions.map((c, i) => {
        const topPos = 58 + i * 34; // pixel offset for handle and label (kept from original)
        const handleId = `out-${c.id || i}`;
        return (
          <React.Fragment key={handleId}>
            <Handle
              type="source"
              id={handleId}
              position={Position.Right}
              style={{
                top: topPos,
                background: "#db2777",
                width: 12,
                height: 12,
              }}
            />
            {/* small label next to handle to identify branch on the canvas */}
            <div
              style={{ top: topPos - 8, right: 40 }}
              className="position-absolute pointer-events-none small fw-medium"
            >
              <span style={{ color: "#db2777" }}>{c.label || `Cond ${i + 1}`}</span>
            </div>
          </React.Fragment>
        );
      })}

      {/* Default outgoing handle (bottom) */}
      <Handle
        type="source"
        position={Position.Bottom}
        id="out-default"
        style={{ left: "50%", background: "#6b7280" /* gray-400 */ }}
      />
      <div
        style={{ bottom: 10, left: "50%" }}
        className="position-absolute small text-muted pointer-events-none"
      >
        default
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
