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
        <div
            ref={ref}
            style={{ position: "absolute", top: 8, right: 8, zIndex: 20 }}
            className="dropdown"
        >
            <button
                aria-label="Node menu"
                onClick={() => setOpen((s) => !s)}
                className="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center"
                style={{ width: 28, height: 28, padding: 0 }}
                type="button"
            >
                ⋮
            </button>

            {open && (
                <div
                    className="dropdown-menu show shadow-sm border"
                    style={{ minWidth: 160 }}
                >
                    {extraItems.map((it, i) => (
                        <button
                            key={i}
                            onClick={() => {
                                setOpen(false);
                                it.onClick && it.onClick();
                            }}
                            className="dropdown-item"
                            type="button"
                        >
                            {it.label}
                        </button>
                    ))}

                    <button
                        onClick={() => {
                            setOpen(false);
                            onDelete && onDelete();
                        }}
                        className="dropdown-item text-danger"
                        type="button"
                    >
                        Delete block
                    </button>
                </div>
            )}
        </div>
    );
}
