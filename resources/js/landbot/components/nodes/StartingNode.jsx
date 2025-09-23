import { Handle, Position } from "@xyflow/react";
import { AddButton } from "../index";

export default function StartingNode({ id, data }) {
  return (
    <div className="bg-white border rounded-3 p-3 shadow w-full position-relative">
      {/* Arrow AddButton (visual) */}
      <AddButton id={id} onAdd={data?.onAddClick} />

      {/* Node content */}
      <div className="d-flex align-items-center gap-2">
        <span>🏁</span>
        <div>
          <p className="fw-bold mb-0">Starting point</p>
          <p className="small text-muted mb-0">Where your bot begins</p>
        </div>
      </div>

      {/* Source handle aligned under the arrow button */}
      <Handle
        type="source"
        id="arrow"
        position={Position.Right}
        style={{
          background: "transparent", // invisible
          border: "none",
          width: 12,
          height: 12,
          right: -6,
          top: "50%",
          transform: "translateY(-50%)",
        }}
      />
    </div>
  );
}
