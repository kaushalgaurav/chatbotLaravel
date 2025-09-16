import { Handle, Position, useReactFlow } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

export default function RatingNode({ data, id }) {
  const { setNodes } = useReactFlow();

  const question = data?.question ?? "How would you rate us?";

  // Update question
  const updateQuestion = (value) => {
    setNodes((nds) =>
      nds.map((n) =>
        n.id === id ? { ...n, data: { ...n.data, question: value } } : n
      )
    );
  };

  return (
    <div
      className="bg-white border rounded p-3 shadow position-relative"
      style={{
        width: "70%", // Tailwind w-50
        borderLeft: "4px solid #ffc107", // Bootstrap warning yellow
      }}
    >
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />

      <p className="fw-bold mb-2">⭐ Rating</p>

      {/* Editable Question */}
      <input
        type="text"
        value={question}
        onChange={(e) => updateQuestion(e.target.value)}
        placeholder="Enter your rating question..."
        className="form-control form-control-sm mb-3"
      />

      {/* Column style stars */}
      <div className="d-flex flex-column gap-2">
        {[1, 2, 3, 4, 5].map((s) => (
          <div
            key={s}
            className="d-flex align-items-center justify-content-between position-relative py-1"
          >
            <span className="small">{"⭐".repeat(s)}</span>
            <Handle
              type="source"
              position={Position.Right}
              id={`star-${s}`}
              style={{
                top: "50%",
                transform: "translateY(-50%)",
                right: "-8px",
                position: "absolute",
                background: "#ffc107", // Bootstrap yellow
              }}
            />
          </div>
        ))}
      </div>

      {/* Input handle (previous node) */}
      <Handle
        type="target"
        position={Position.Top}
        style={{ background: "#ffc107" }}
      />
    </div>
  );
}
