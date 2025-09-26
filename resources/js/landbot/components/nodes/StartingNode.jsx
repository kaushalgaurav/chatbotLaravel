import { Handle, Position } from "@xyflow/react";
import { AddButton } from "../index";

export default function StartingNode({ id, data }) {
  return (
    <div className="bg-white border rounded-3 p-3 shadow w-full position-relative">
      <AddButton id={id} onAdd={data?.onAddClick} />

      <div className="d-flex align-items-center gap-2">
        <span>🏁</span>
        <div>
          <p className="fw-bold mb-0">Starting point</p>
          <p className="small text-muted mb-0">Where your bot begins</p>
        </div>
      </div>

      <Handle
        type="source"
        id="arrow"
        position={Position.Right}
        style={{
          background: "transparent",
          border: "none",
          width: 12,
          height: 12,
          right: -6,
          top: "50%",
          transform: "translateY(-50%)",
          // make sure handle sits *behind* the add button
          zIndex: 1,
          // if you don't need the handle to accept clicks at that exact spot,
          // you can also disable pointer events and leave it for visual only:
          // pointerEvents: 'none'
        }}
      />
    </div>
  );
}
