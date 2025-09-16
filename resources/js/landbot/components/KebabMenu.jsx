// src/components/KebabMenu.jsx
import React, { useState, useRef, useEffect } from "react";

export default function KebabMenu({ onDelete, extraItems = [] }) {
    const [open, setOpen] = useState(false);
    const ref = useRef(null);

    useEffect(() => {
        function onDoc(e) {
            if (!ref.current) return;
            if (!ref.current.contains(e.target)) setOpen(false);
        }
        document.addEventListener("pointerdown", onDoc);
        return () => document.removeEventListener("pointerdown", onDoc);
    }, []);

    return (
        <div ref={ref} style={{ position: "absolute", top: 8, right: 8, zIndex: 20 }}>
            <button
                aria-label="Node menu"
                onClick={() => setOpen((s) => !s)}
                className="p-1 rounded hover:bg-gray-100"
                style={{ width: 28, height: 28 }}
            >
                ⋮
            </button>

            {open && (
                <div className="bg-white border rounded shadow-sm text-sm" style={{ minWidth: 160 }}>
                    {extraItems.map((it, i) => (
                        <div key={i} onClick={() => { setOpen(false); it.onClick && it.onClick(); }} className="px-3 py-2 hover:bg-gray-50 cursor-pointer">
                            {it.label}
                        </div>
                    ))}

                    <div
                        onClick={() => { setOpen(false); onDelete && onDelete(); }}
                        className="px-3 py-2 hover:bg-red-50 cursor-pointer text-red-600"
                    >
                        Delete block
                    </div>
                </div>
            )}
        </div>
    );
}
