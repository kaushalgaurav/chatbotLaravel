// src/components/PopupMenu.jsx
import React from "react";

export default function PopupMenu({ x = 0, y = 0, onSelect, onClose }) {
  const items = [
    { type: "message", label: "💬 Message" },
    { type: "question", label: "❓ Question" },
    { type: "buttons", label: "🔘 Buttons" },
    { type: "yesno", label: "✅ Yes / ❌ No" },
    { type: "rating", label: "⭐ Rating" },
    // { type: "condition", label: "⚖️ Condition" },
    // { type: "formula", label: "📐 Formula" },
  ];

  return (
    <div
    className="position-absolute bg-white border rounded shadow p-3"
 style={{ fontSize: "15px", left: x, top: y, zIndex: 1050 }}
      role="menu"
    >
      <div className="text-muted small mb-1">Add node</div>

      <div className="list-group list-group-flush">
        {items.map((item) => (
          <button
            key={item.type}
            type="button"
            className="list-group-item list-group-item-action py-2 px-3 rounded fw-medium"
            onClick={() => {
              onSelect(item.type);
              onClose();
            }}
          >
            {item.label}
          </button>
        ))}
      </div>

      <div className="mt-1 d-flex justify-content-end">
        <button
          type="button"
          className="btn btn-link btn-sm text-danger p-0"
          onClick={onClose}
        >
          ✖ Close
        </button>
      </div>
    </div>
  );
}
