// src/components/Topbar.jsx
import React, { useState } from "react";
import { NavLink } from "react-router-dom";

export default function Topbar({ onTest, onPublish, publishing = false }) {
  const [botName, setBotName] = useState("My Chatbot");
  const [editing, setEditing] = useState(false);

  const linkClass =
    "text-decoration-none text-dark px-2 py-1 position-relative";

  return (
    <div className="d-flex align-items-center px-5 py-3 bg-white shadow-sm position-relative">
      {/* Left: Logo + Bot Name */}
      <div className="d-flex align-items-center gap-3">
        <img
          src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1szHkm1MJL1Fd9d-QoC2rYC-mBxyAqRWkTA&s"
          alt="Logo"
          style={{ width: 36, height: 36 }}
        />

        {editing ? (
          <input
            type="text"
            className="form-control form-control-sm fs-4 fw-bold border-0 border-bottom"
            style={{ width: "220px" }}
            value={botName}
            autoFocus
            onChange={(e) => setBotName(e.target.value)}
            onBlur={() => setEditing(false)}
            onKeyDown={(e) => e.key === "Enter" && setEditing(false)}
          />
        ) : (
          <h4
            className="fw-bold mb-0 text-truncate"
            style={{ cursor: "pointer", maxWidth: "220px" }}
            onClick={() => setEditing(true)}
            title="Click to edit bot name"
          >
            {botName}
          </h4>
        )}
      </div>

      {/* Center: Navigation */}
      <div className="position-absolute start-50 translate-middle-x">
        <div className="d-flex gap-5 fw-bold fs-4">
          <NavLink
            to="/"
            className={({ isActive }) =>
              `${linkClass} ${isActive ? "active-link" : ""}`
            }
          >
            Build
          </NavLink>
          <NavLink
            to="/design"
            className={({ isActive }) =>
              `${linkClass} ${isActive ? "active-link" : ""}`
            }
          >
            Design
          </NavLink>
          <NavLink
            to="/setting"
            className={({ isActive }) =>
              `${linkClass} ${isActive ? "active-link" : ""}`
            }
          >
            Setting
          </NavLink>
          <NavLink
            to="/share"
            className={({ isActive }) =>
              `${linkClass} ${isActive ? "active-link" : ""}`
            }
          >
            Share
          </NavLink>
          <NavLink
            to="/analyze"
            className={({ isActive }) =>
              `${linkClass} ${isActive ? "active-link" : ""}`
            }
          >
            Analyze
          </NavLink>
        </div>
      </div>

      {/* Right: Buttons */}
      <div className="ms-auto d-flex align-items-center gap-4">
        <button onClick={onTest} className="btn btn-success px-5 py-2 fs-5">
          Test this bot
        </button>

        <button
          onClick={onPublish}
          disabled={publishing}
          className={`btn px-5 py-2 fs-5 text-white ${
            publishing ? "btn-secondary disabled" : "btn-danger"
          }`}
          aria-busy={publishing}
        >
          {publishing ? (
            <span className="d-inline-flex align-items-center gap-2">
              <span
                className="spinner-border spinner-border-sm"
                role="status"
                aria-hidden="true"
              ></span>
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
