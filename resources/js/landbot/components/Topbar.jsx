// src/components/Topbar.jsx
import React from "react";
import { Link } from "react-router-dom";

export default function Topbar({ onTest, onPublish, publishing = false }) {
  return (
    <div className="d-flex align-items-center justify-content-between px-4 py-2 bg-white shadow-sm">
      {/* Left navigation */}
      <div className="d-flex gap-4 fw-medium">
        <Link to="/" className="text-decoration-none">Build</Link>
        <Link to="/share" className="text-decoration-none">Share</Link>
        <button className="btn btn-link p-0">Analyze</button>
      </div>

      {/* Right buttons */}
      <div className="d-flex align-items-center gap-3">
        <button
          onClick={onTest}
          className="btn btn-success px-4 py-1"
        >
          Test this bot
        </button>

        <button
          onClick={onPublish}
          disabled={publishing}
          className={`btn px-4 py-1 text-white ${publishing ? "btn-secondary disabled" : "btn-danger"}`}
          aria-busy={publishing}
        >
          {publishing ? (
            <span className="d-inline-flex align-items-center gap-2">
              <span className="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              Publishing...
            </span>
          ) : (
            "Publish"
          )}
        </button>
      </div>
    </div>
  );
}
