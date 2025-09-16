import { Handle, Position, useReactFlow } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

export default function MessageNode({ data, id }) {
  const { setNodes } = useReactFlow();

  const handleChange = (e) => {
    const newText = e.target.value;
    setNodes((nds) =>
      nds.map((n) =>
        n.id === id ? { ...n, data: { ...n.data, text: newText } } : n
      )
    );
  };

  return (
    <div
      className="bg-white border rounded p-3 shadow w-100 position-relative"
      style={{
        width: "50%", // w-50 in Tailwind — use inline width or adjust with a Bootstrap grid if you prefer
        borderLeft: "4px solid #6B7280", // Tailwind's border-l-4 border-gray-500
      }}
    >
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />

      <p className="fw-bold mb-2">💬 Message</p>

      <textarea
        value={data.text || ""}
        onChange={handleChange}
        placeholder="Bot message..."
        className="form-control form-control-sm w-100 mt-1"
        rows={2}
      />

      {/* Handles */}
      <Handle type="target" position={Position.Top} className="bg-secondary" />
      <Handle type="source" position={Position.Bottom} className="bg-secondary" />
    </div>
  );
}
