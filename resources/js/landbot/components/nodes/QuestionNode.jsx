import React, { useEffect, useRef, useState } from "react";
import { Handle, Position, useReactFlow } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

export default function QuestionNode({ data, id }) {
  const { setNodes } = useReactFlow();

  // local state to avoid caret/space issues while typing
  const [localVal, setLocalVal] = useState(() => data?.label ?? "");
  const focusedRef = useRef(false);
  const inputRef = useRef(null);

  // helpers that mirror NodeInspector behaviour
  const escapeHtml = (str = "") =>
    String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");

  const plainToHtml = (plain = "") =>
    `<div style="white-space: pre-wrap;">${escapeHtml(plain)}</div>`;

  // sync external updates into localVal only when not focused
  useEffect(() => {
    const incoming = data?.label ?? "";
    if (!focusedRef.current && incoming !== localVal) {
      setLocalVal(incoming);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data?.label]);

  const commitToNodes = (newLabel) => {
    setNodes((nds) =>
      nds.map((n) =>
        n.id === id
          ? {
              ...n,
              data: {
                ...n.data,
                label: newLabel,
                labelHtml: plainToHtml(newLabel),
              },
            }
          : n
      )
    );
  };

  const handleChange = (e) => {
    const newLabel = e.target.value;
    setLocalVal(newLabel);
    commitToNodes(newLabel);
  };

  return (
    <div
      className="bg-white border rounded p-3 w-100 shadow position-relative"
      style={{
        width: "70%", // Tailwind w-50
        borderLeft: "4px solid #0d6efd", // Tailwind border-l-4 border-blue-500
      }}
    >
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />

      <p className="fw-bold mb-2">❓ Question</p>

      <input
        ref={inputRef}
        value={localVal}
        onChange={handleChange}
        placeholder="Ask your question..."
        className="form-control form-control-sm text-secondary mt-1"
        onFocus={() => {
          focusedRef.current = true;
        }}
        onBlur={() => {
          focusedRef.current = false;
          const finalVal = inputRef.current ? inputRef.current.value : localVal;
          setLocalVal(finalVal);
          commitToNodes(finalVal);
        }}
        // capture-phase handlers so React Flow doesn't intercept space etc.
        onKeyDownCapture={(e) => {
          try {
            e.stopPropagation();
            if (e.nativeEvent && typeof e.nativeEvent.stopImmediatePropagation === "function") {
              e.nativeEvent.stopImmediatePropagation();
            }
          } catch {}
        }}
        onKeyUpCapture={(e) => {
          try {
            e.stopPropagation();
            if (e.nativeEvent && typeof e.nativeEvent.stopImmediatePropagation === "function") {
              e.nativeEvent.stopImmediatePropagation();
            }
          } catch {}
        }}
      />
    </div>
  );
}
