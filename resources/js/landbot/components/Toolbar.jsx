export default function Toolbar({ onZoomIn, onZoomOut, onFitView, onUndo, onRedo }) {
  return (
    <div
      className="position-absolute bg-white rounded shadow p-2 d-flex gap-2"
      style={{ bottom: "1.25rem", right: "1.25rem" }} // bottom-5 right-5 ≈ 1.25rem
    >
      <button
        onClick={onZoomIn}
        className="btn btn-light btn-sm"
        title="Zoom In"
      >
        ➕
      </button>
      <button
        onClick={onZoomOut}
        className="btn btn-light btn-sm"
        title="Zoom Out"
      >
        ➖
      </button>
      <button
        onClick={onFitView}
        className="btn btn-light btn-sm"
        title="Fit View"
      >
        🔲
      </button>
      <button
        onClick={onUndo}
        className="btn btn-light btn-sm"
        title="Undo"
      >
        ↩️
      </button>
      <button
        onClick={onRedo}
        className="btn btn-light btn-sm"
        title="Redo"
      >
        ↪️
      </button>
    </div>
  );
}
