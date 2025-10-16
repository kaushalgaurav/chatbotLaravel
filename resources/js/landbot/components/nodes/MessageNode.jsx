import React, { useEffect, useRef, useState } from "react";
import { Handle, Position, useReactFlow } from "@xyflow/react";
import { AddButton, KebabMenu } from "../index";

export default function MessageNode({ data, id }) {
  const { setNodes } = useReactFlow();

  // local controlled value so React re-renders won't clobber caret while typing
  const [localVal, setLocalVal] = useState(() => data?.text ?? "");
  const focusedRef = useRef(false);
  const taRef = useRef(null);

  // helpers (must match NodeInspector's plainToHtml)
  const escapeHtml = (str = "") =>
    String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");

  const plainToHtml = (plain = "") =>
    `<div style="white-space: pre-wrap;">${escapeHtml(plain)}</div>`;

  // sync incoming external changes into localVal, but only if not focused
  useEffect(() => {
    const incoming = data?.text ?? "";
    if (!focusedRef.current && incoming !== localVal) {
      setLocalVal(incoming);
    }
    // intentionally only depend on data?.text
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data?.text]);

  const commitToNodes = (newText) => {
    setNodes((nds) =>
      nds.map((n) =>
        n.id === id
          ? {
              ...n,
              data: {
                ...n.data,
                text: newText,
                textHtml: plainToHtml(newText),
              },
            }
          : n
      )
    );
  };

  const handleChange = (e) => {
    const newText = e.target.value;
    setLocalVal(newText);
    commitToNodes(newText);
  };

  return (
    <div
      className="bg-white border rounded p-3 shadow w-100 position-relative"
      style={{
        width: "50%",
        borderLeft: "4px solid #6B7280",
      }}
    >
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />

      <p className="fw-bold mb-2">💬 Message</p>

      <textarea
        ref={taRef}
        value={localVal}
        onChange={handleChange}
        placeholder="Bot message..."
        className="form-control form-control-sm w-100 mt-1"
        rows={2}
        onFocus={() => {
          focusedRef.current = true;
        }}
        onBlur={() => {
          focusedRef.current = false;
          // commit final value on blur (defensive)
          const finalVal = taRef.current ? taRef.current.value : localVal;
          setLocalVal(finalVal);
          commitToNodes(finalVal);
        }}
        // capture-phase handlers to keep React Flow from intercepting keys (safety)
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
