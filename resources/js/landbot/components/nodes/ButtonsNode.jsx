import { Handle, Position, useReactFlow } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

export default function ButtonsNode({ data, id }) {
  const { setNodes } = useReactFlow();

  const question = data?.question ?? "Ask your question here...";
  const options = data?.options ?? ["Option 1", "Option 2"];

  // Update the question text
  const handleQuestionChange = (e) => {
    const newQuestion = e.target.value;
    setNodes((nds) =>
      nds.map((n) =>
        n.id === id
          ? { ...n, data: { ...n.data, question: newQuestion, options: [...options] } }
          : n
      )
    );
  };

  // Update a specific option
  const handleOptionChange = (i, value) => {
    const newOptions = [...options];
    newOptions[i] = value;

    setNodes((nds) =>
      nds.map((n) =>
        n.id === id
          ? { ...n, data: { ...n.data, question, options: newOptions } }
          : n
      )
    );
  };

  return (
    <div
      className="bg-white border rounded p-3 shadow position-relative"
      style={{
        width: "70%", // Tailwind w-50
        borderLeft: "4px solid #6f42c1", // purple-500 equivalent
      }}
    >
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />

      <p className="fw-bold mb-2">🔘 Buttons</p>

      {/* Editable Question */}
      <input
        type="text"
        value={question}
        onChange={handleQuestionChange}
        placeholder="Enter your question..."
        className="form-control form-control-sm mb-2"
      />

      {/* Editable Options */}
      <div className="d-flex flex-column gap-2">
        {options.map((opt, i) => (
          <div key={i} className="d-flex align-items-center position-relative py-1">
            <input
              type="text"
              value={opt}
              onChange={(e) => handleOptionChange(i, e.target.value)}
              className="form-control form-control-sm flex-grow-1 me-2"
            />

            <Handle
              type="source"
              position={Position.Right}
              id={`option-${i}`}
              // Position the handle slightly outside the option row (like your Tailwind negative right)
              style={{
                top: "50%",
                transform: "translateY(-50%)",
                right: "-8px",
                position: "absolute",
                background: "#6f42c1", // purple-500
              }}
            />
          </div>
        ))}
      </div>

      {/* Input handle (previous node) */}
      <Handle
        type="target"
        position={Position.Top}
        // top handle color
        style={{ background: "#6f42c1" }}
      />
    </div>
  );
}
