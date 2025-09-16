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
        position={Position.Bottom}
        className="bg-primary"
      />
    </div>
  );
}
