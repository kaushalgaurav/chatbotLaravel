// src/components/KebabMenu.jsx
import React, { useState, useRef, useEffect } from "react";
import { createPortal } from "react-dom";

/**
 * KebabMenu
 * - Renders the dropdown via portal so it won't be clipped/overlaid by the flow canvas or other node controls.
 * - Stops propagation on mousedown/click to prevent canvas drag from stealing events.
 */
export default function KebabMenu({ onDelete, extraItems = [] }) {
  const [open, setOpen] = useState(false);
  const [anchorRect, setAnchorRect] = useState(null);
  const containerRef = useRef(null);
  const buttonRef = useRef(null);

  // Close on outside pointerdown (works whether the menu is portal or not)
  useEffect(() => {
    function onDoc(e) {
      // if menu isn't open, nothing to do
      if (!open) return;
      // if click is inside our node container (button or portal menu), do nothing
      if (containerRef.current && containerRef.current.contains(e.target)) return;
      // also if portal menu exists and contains target, do nothing
      const portalEl = document.getElementById("kebab-menu-portal");
      if (portalEl && portalEl.contains(e.target)) return;
      setOpen(false);
    }
    document.addEventListener("pointerdown", onDoc);
    return () => document.removeEventListener("pointerdown", onDoc);
  }, [open]);

  // toggle menu, set anchor rect for portal positioning
  const toggle = (e) => {
    // stop canvas drag/selection from starting
    e.stopPropagation();
    e.preventDefault();
    const rect = buttonRef.current?.getBoundingClientRect() ?? null;
    setAnchorRect(rect);
    setOpen((s) => !s);
  };

  // handle clicking an item inside menu (stop propagation so canvas doesn't get it)
  const handleItemClick = (cb) => (e) => {
    e.stopPropagation();
    e.preventDefault();
    setOpen(false);
    cb && cb();
  };

  // Render the portal menu. Positioned to the right-top of the kebab button by default.
  const PortalMenu = () => {
    if (!anchorRect) return null;
    const top = anchorRect.top + window.scrollY - 8; // small vertical shift
    const left = anchorRect.left + window.scrollX + anchorRect.width + 8; // to the right of button
    const style = {
      position: "absolute",
      top: `${top}px`,
      left: `${left}px`,
      zIndex: 20000, // much higher than node controls and handles
      minWidth: 160,
      background: "#fff",
      borderRadius: 6,
      boxShadow: "0 6px 18px rgba(0,0,0,0.12)",
      overflow: "hidden",
    };

    return createPortal(
      <div
        id="kebab-menu-portal"
        style={style}
        // prevent the canvas from stealing mousedown while interacting with portal menu
        onMouseDown={(e) => e.stopPropagation()}
        onClick={(e) => e.stopPropagation()}
      >
        <div style={{ padding: 6 }}>
          {extraItems.map((it, i) => (
            <button
              key={i}
              type="button"
              className="dropdown-item"
              style={{ width: "100%", textAlign: "left" }}
              onClick={handleItemClick(it.onClick)}
            >
              {it.label}
            </button>
          ))}

          <button
            type="button"
            className="dropdown-item text-danger"
            style={{ width: "100%", textAlign: "left", marginTop: 6,fontSize: "22px", padding: "10px 10px" }}
            onClick={handleItemClick(onDelete)}
          >
            Delete Node
          </button>
        </div>
      </div>,
      document.body
    );
  };

  return (
    // containerRef keeps track of node-local button so outside-click logic can include it
    <div ref={containerRef} style={{ position: "absolute", top: 8, right: 8, zIndex: 20 }}>
      <button
        ref={buttonRef}
        aria-label="Node menu"
        onMouseDown={(e) => e.stopPropagation()} // prevent flow drag start
        onClick={toggle}
        className="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center"
        style={{ width: 28, height: 28, padding: 0 }}
        type="button"
      >
        ⋮
      </button>

      {open && <PortalMenu />}
    </div>
  );
}
