import React from "react";
import { Handle, Position, useReactFlow } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

export default function ButtonsNode({ data = {}, id }) {
  const { setNodes } = useReactFlow();

  const question = data?.question ?? "Ask your question here...";
  const options = Array.isArray(data?.options) ? data.options : ["Option 1", "Option 2"];
  const fallbackLabel = data?.fallbackLabel ?? "Any of the above";

  // Update question
  const handleQuestionChange = (e) => {
    const newQuestion = e.target.value;
    setNodes((nds) =>
      nds.map((n) => (n.id === id ? { ...n, data: { ...n.data, question: newQuestion, options: [...options] } } : n))
    );
  };

  // Update option
  const handleOptionChange = (i, value) => {
    const newOptions = [...options];
    newOptions[i] = value;
    setNodes((nds) =>
      nds.map((n) => (n.id === id ? { ...n, data: { ...n.data, question, options: newOptions } } : n))
    );
  };

  // Update fallback
  const handleFallbackChange = (value) => {
    setNodes((nds) =>
      nds.map((n) => (n.id === id ? { ...n, data: { ...n.data, fallbackLabel: value } } : n))
    );
  };

  // Call parent's onAddClick with (nodeId, optIndex) where optIndex is index or "fallback"
  const callAdd = (optIndex) => {
    if (typeof data?.onAddClick === "function") {
      data.onAddClick(id, optIndex);
    } else {
      console.warn("ButtonsNode: data.onAddClick not provided", { id, optIndex });
    }
  };

  return (
    <div
      className="bg-white border rounded p-3 w-100 shadow position-relative"
      style={{ width: "70%", borderLeft: "4px solid #6f42c1" }}
    >
      <KebabMenu onDelete={data?.onDelete} />
      {/* remove the generic AddButton here (we will render per-option AddButton) */}

      <p className="fw-bold mb-2">🔘 Buttons</p>

      <input
        type="text"
        value={question}
        onChange={handleQuestionChange}
        placeholder="Enter your question..."
        className="form-control form-control-sm mb-2"
      />

      <div className="d-flex flex-column gap-2">
        {options.map((opt, i) => (
          <div key={i} className="d-flex align-items-center position-relative py-1">
            <input
              type="text"
              value={opt}
              onChange={(e) => handleOptionChange(i, e.target.value)}
              className="form-control form-control-sm flex-grow-1 me-2"
            />

            {/* Per-option AddButton component - uses same AddButton component you already have */}
            {/* Pass some meaningful props so AddButton can identify which option (you may adapt its props) */}
            <div style={{ marginRight: 8 }}>
              <AddButton
                id={id}
                // prefer passing option index so parent knows which handle to connect
                onAdd={() => callAdd(i)}
                // optional: if AddButton expects onAddClick prop name, adapt accordingly
              />
            </div>

            {/* Per-option handle */}
            <Handle
              type="source"
              position={Position.Right}
              id={`option-${i}`}
              style={{
                top: "50%",
                transform: "translateY(-50%)",
                right: "-8px",
                position: "absolute",
                background: "#6f42c1",
                width: 12,
                height: 12,
                borderRadius: 12,
                border: "2px solid #fff",
              }}
            />
          </div>
        ))}

        {/* Fallback row with its own AddButton and handle */}
        <div className="d-flex align-items-center position-relative py-1">
          <input
            type="text"
            value={fallbackLabel}
            onChange={(e) => handleFallbackChange(e.target.value)}
            className="form-control form-control-sm flex-grow-1 me-2"
          />

          <div style={{ marginRight: 8 }}>
            <AddButton id={id} onAdd={() => callAdd("fallback")} />
          </div>

          <Handle
            type="source"
            position={Position.Right}
            id={`fallback`}
            style={{
              top: "50%",
              transform: "translateY(-50%)",
              right: "-8px",
              position: "absolute",
              background: "#0ea5e9",
              width: 12,
              height: 12,
              borderRadius: 12,
              border: "2px solid #fff",
            }}
          />
        </div>
      </div>
    </div>
  );
}
