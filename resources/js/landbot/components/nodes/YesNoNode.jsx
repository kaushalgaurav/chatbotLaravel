import { Handle, Position, useReactFlow } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

export default function YesNoNode({ data, id }) {
  const { setNodes } = useReactFlow();

  const question = data?.question ?? "Ask your Yes/No question here...";
  const yesLabel = data?.yesLabel ?? "Yes";
  const noLabel = data?.noLabel ?? "No";

  // Update question
  const updateQuestion = (value) => {
    setNodes((nds) =>
      nds.map((n) =>
        n.id === id
          ? { ...n, data: { ...n.data, question: value, yesLabel, noLabel } }
          : n
      )
    );
  };

  // Update Yes/No labels
  const updateLabel = (key, value) => {
    setNodes((nds) =>
      nds.map((n) =>
        n.id === id
          ? {
              ...n,
              data: {
                ...n.data,
                question,
                yesLabel: key === "yesLabel" ? value : yesLabel,
                noLabel: key === "noLabel" ? value : noLabel,
              },
            }
          : n
      )
    );
  };

  return (
    <div
      className="bg-white border rounded p-3 shadow position-relative"
      style={{
        width: "70%", // Tailwind w-50 equivalent
        borderLeft: "4px solid #198754", // Bootstrap 'success' green (#198754)
      }}
    >
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />

      <p className="fw-bold mb-2">✅ Yes / ❌ No</p>

      {/* Editable Question */}
      <input
        type="text"
        value={question}
        onChange={(e) => updateQuestion(e.target.value)}
        placeholder="Enter your question..."
        className="form-control form-control-sm mb-2"
      />

      <div className="d-flex flex-column gap-2">
        {/* Yes option */}
        <div className="d-flex align-items-center position-relative py-1">
          <input
            type="text"
            value={yesLabel}
            onChange={(e) => updateLabel("yesLabel", e.target.value)}
            className="form-control form-control-sm flex-grow-1 me-2"
          />
          <Handle
            type="source"
            position={Position.Right}
            id="yes"
            style={{
              top: "50%",
              transform: "translateY(-50%)",
              right: "-8px",
              position: "absolute",
              background: "#198754", // green
            }}
          />
        </div>

        {/* No option */}
        <div className="d-flex align-items-center position-relative py-1">
          <input
            type="text"
            value={noLabel}
            onChange={(e) => updateLabel("noLabel", e.target.value)}
            className="form-control form-control-sm flex-grow-1 me-2"
          />
          <Handle
            type="source"
            position={Position.Right}
            id="no"
            style={{
              top: "50%",
              transform: "translateY(-50%)",
              right: "-8px",
              position: "absolute",
              background: "#198754", // green
            }}
          />
        </div>
      </div>

      {/* Input handle (previous node) */}
      <Handle
        type="target"
        position={Position.Top}
        style={{ background: "#198754" }}
      />
    </div>
  );
}
