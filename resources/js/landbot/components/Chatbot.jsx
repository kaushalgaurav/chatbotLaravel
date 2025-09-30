// src/components/Chatbot.jsx
import React from "react";
import useChatbotEngine from "../hooks/useChatbotEngine";

export default function Chatbot({ nodes = [], edges = [], onClose }) {
  const {
    currentNode,
    history,
    variables,
    inputValue,
    setInputValue,
    submitInput,
    clickButton,
  } = useChatbotEngine({ nodes, edges });

  const panelStyle = { width: "24rem", zIndex: 1050 }; // w-96 ≈ 24rem

  if (!currentNode) {
    return (
      <div
        className="position-fixed top-0 end-0 h-100 bg-white shadow d-flex flex-column"
        style={panelStyle}
      >
        <div className="p-3 bg-primary text-white d-flex justify-content-between align-items-center">
          <p className="mb-0">Chatbot Preview</p>
          <button type="button" className="btn btn-sm btn-light" onClick={onClose} aria-label="Close">
            ❌
          </button>
        </div>

        <div className="flex-grow-1 d-flex align-items-center justify-content-center text-muted p-3">
          No starting node found in this flow.
        </div>
      </div>
    );
  }

  return (
    <div
      className="position-fixed top-0 end-0 h-100 bg-white shadow d-flex flex-column"
      style={panelStyle}
    >
      <div className="p-3 bg-primary text-white d-flex justify-content-between align-items-center">
        <p className="mb-0">Chatbot Preview</p>
        <button type="button" className="btn btn-sm btn-light" onClick={onClose} aria-label="Close">
          ❌
        </button>
      </div>

      {/* history */}
      <div className="flex-grow-1 overflow-auto p-3 d-flex flex-column gap-3">
        {history.map((m, i) => (
          <div
            key={i}
            className={`p-2 rounded-3 ${m.sender === "bot" ? "bg-light align-self-start" : "bg-primary text-white align-self-end"}`}
            style={{ maxWidth: "60%" }}
          >
            {m.text}
          </div>
        ))}

        {/* Node controls: buttons / yesno / rating */}
        {currentNode.type === "buttons" && (
          <div className="d-flex flex-column gap-2 mt-2">
            {(currentNode.data?.options || []).map((opt, i) => (
              <button
                key={i}
                className="btn btn-outline-secondary btn-sm text-start"
                onClick={() => clickButton(opt, `option-${i}`)}
                type="button"
              >
                {opt}
              </button>
            ))}

            {currentNode.data?.fallbackLabel && (
              <button
                className="btn btn-outline-secondary btn-sm text-start"
                onClick={() => clickButton(currentNode.data.fallbackLabel, "fallback")}
                type="button"
                >
                  {currentNode.data.fallbackLabel}
                </button>            
            )}
          </div>
        )}

        {currentNode.type === "yesno" && (
          <div className="d-flex flex-column gap-2 mt-2">
            <button
              type="button"
              onClick={() => clickButton(currentNode.data?.yesLabel || "Yes", "yes")}
              className="btn btn-success btn-sm"
            >
              {currentNode.data?.yesLabel || "Yes"}
            </button>
            <button
              type="button"
              onClick={() => clickButton(currentNode.data?.noLabel || "No", "no")}
              className="btn btn-danger btn-sm"
            >
              {currentNode.data?.noLabel || "No"}
            </button>
          </div>
        )}

        {currentNode.type === "rating" && (
          <div className="d-flex flex-column gap-2 mt-2">
            {[1, 2, 3, 4, 5].map((s) => (
              <button
                key={s}
                type="button"
                onClick={() => clickButton(`${s} Star${s > 1 ? "s" : ""}`, `star-${s}`)}
                className="btn btn-warning btn-sm text-start"
              >
                {"⭐".repeat(s)}
              </button>
            ))}
          </div>
        )}
      </div>

      {/* Input for question */}
      {currentNode.type === "question" && (
        <div className="p-3 d-flex gap-2 align-items-center">
          <input
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            placeholder="Type your answer..."
            className="form-control me-2"
          />
          <button onClick={submitInput} className="btn btn-primary" type="button">
            Send
          </button>
        </div>
      )}
    </div>
  );
}
