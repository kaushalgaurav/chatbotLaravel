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

  const bg =
    toast.type === "success" ? "bg-green-50 border-green-400" :
    toast.type === "error" ? "bg-red-50 border-red-400" :
    "bg-blue-50 border-blue-300";

  return (
    <div className={`fixed bottom-6 right-6 z-50 p-3 border rounded shadow ${bg}`}>
      <div className="text-sm">{toast.message}</div>
    </div>
  );
}
