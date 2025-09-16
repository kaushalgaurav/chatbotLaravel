import { Handle, Position, useReactFlow } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

export default function QuestionNode({ data, id }) {
  const { setNodes } = useReactFlow();

  const handleChange = (e) => {
    const newLabel = e.target.value;
    setNodes((nds) =>
      nds.map((n) =>
        n.id === id ? { ...n, data: { ...n.data, label: newLabel } } : n
      )
    );
  };

  return (
    <div
      className="bg-white border rounded p-3 shadow position-relative"
      style={{
        width: "70%", // Tailwind w-50
        borderLeft: "4px solid #0d6efd", // Tailwind border-l-4 border-blue-500
      }}
    >
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />

      <p className="fw-bold mb-2">❓ Question</p>

      <input
        value={data.label || ""}
        onChange={handleChange}
        placeholder="Ask your question..."
        className="form-control form-control-sm text-secondary mt-1"
      />

      {/* Handles */}
      <Handle type="target" position={Position.Top} className="bg-primary" />
      <Handle type="source" position={Position.Bottom} className="bg-primary" />
    </div>
  );
}
