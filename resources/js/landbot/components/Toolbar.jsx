import { Plus, Minus, Square, Undo2, Redo2 } from "lucide-react";

export default function Toolbar({
  zoom = 1, // default to 100% when prop missing
  onZoomIn,
  onZoomOut,
  onFitView,
  onUndo,
  onRedo,
}) {
  // Determine display string safely:
  // - If zoom is a finite number:
  //    * if > 1.5, assume it's already a percentage value (e.g. 84) -> show "84%"
  //    * otherwise assume it's a fraction (e.g. 0.84) -> show "84%"
  // - If not a finite number, fall back to "100%"
  const displayPercentage = (() => {
    if (typeof zoom === "number" && Number.isFinite(zoom)) {
      if (zoom > 1.5) return `${Math.round(zoom)}%`;
      return `${Math.round(zoom * 100)}%`;
    }
    return "100%";
  })();

  return (
    <div
      className="position-absolute bg-white rounded shadow d-flex align-items-center gap-2 px-3 py-2"
      style={{ bottom: "1.25rem", right: "1.25rem" }}
    >
      {/* Zoom Out */}
      <button
        onClick={onZoomOut}
        className="btn btn-light btn-sm d-flex align-items-center justify-content-center"
        title="Zoom Out"
      >
        <Minus size={22} />
      </button>

      {/* Zoom Percentage */}
      <span className="mx-2 fw-bold">{displayPercentage}</span>

      {/* Zoom In */}
      <button
        onClick={onZoomIn}
        className="btn btn-light btn-sm d-flex align-items-center justify-content-center"
        title="Zoom In"
      >
        <Plus size={22} />
      </button>

      {/* Fit View */}
      <button
        onClick={onFitView}
        className="btn btn-light btn-sm d-flex align-items-center justify-content-center"
        title="Fit View"
      >
        <Square size={22} />
      </button>

      {/* Undo */}
      <button
        onClick={onUndo}
        className="btn btn-light btn-sm d-flex align-items-center justify-content-center"
        title="Undo"
      >
        <Undo2 size={22} />
      </button>

      {/* Redo */}
      <button
        onClick={onRedo}
        className="btn btn-light btn-sm d-flex align-items-center justify-content-center"
        title="Redo"
      >
        <Redo2 size={22} />
      </button>
    </div>
  );
}
