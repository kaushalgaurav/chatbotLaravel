// src/components/Toast.jsx
import React, { useEffect } from "react";

/**
 * Simple toast.
 * props:
 * - toast: { type, message } or null
 * - onClear: function
 */
export default function Toast({ toast, onClear }) {
  useEffect(() => {
    if (!toast) return;
    const t = setTimeout(() => onClear(), 3500);
    return () => clearTimeout(t);
  }, [toast, onClear]);

  if (!toast) return null;

  // Map toast types to Bootstrap contextual classes
  const variant =
    toast.type === "success"
      ? "border-success bg-light"
      : toast.type === "error"
      ? "border-danger bg-light"
      : "border-primary bg-light";

  return (
    <div
      className={`position-fixed p-3 border rounded shadow ${variant}`}
      style={{ bottom: "1.5rem", right: "1.5rem", zIndex: 1050, maxWidth: "300px" }}
    >
      <div className="small">{toast.message}</div>
    </div>
  );
}
