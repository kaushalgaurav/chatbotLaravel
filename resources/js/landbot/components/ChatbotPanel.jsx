// src/components/ChatbotPanel.jsx
import React, { useEffect, useState } from "react";
const Chatbot = React.lazy(() => import("./Chatbot"));

export default function ChatbotPanel({ nodes, edges, onClose, topOffset = 56, width = 360 }) {
  // optional: trap focus here or add ESC to close
  useEffect(() => {
    function onKey(e) { if (e.key === "Escape") onClose(); }
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [onClose]);

  return (
    <div
      className="fixed right-0 z-50 bg-white shadow-lg"
      style={{ top: `${topOffset}px`, bottom: 0, width: `${width}px`, overflow: "auto" }}
    >
      <React.Suspense fallback={<div className="p-4">Loading chatbot…</div>}>
        <Chatbot nodes={nodes} edges={edges} onClose={onClose} />
      </React.Suspense>
    </div>
  );
}
