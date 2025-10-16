// src/components/nodes/ConditionEditor.jsx
import React from "react";
const uuidv4 = () => `${Date.now()}-${Math.floor(Math.random() * 10000)}`;

export default function ConditionEditor({ node, updateNode }) {
  // safe defaults
  const data = node.data || {};
  const conditions = Array.isArray(data.conditions) ? data.conditions : [];
  const defaultEdgeLabel = data.defaultEdgeLabel || "";

  const addCondition = () => {
    const n = { id: uuidv4(), label: "New", variable: "", operator: "==", value: "" };
    updateNode({ ...node, data: { ...data, conditions: [...conditions, n], defaultEdgeLabel } });
  };

  const updateCond = (id, patch) => {
    const updated = conditions.map((c) => (c.id === id ? { ...c, ...patch } : c));
    updateNode({ ...node, data: { ...data, conditions: updated, defaultEdgeLabel } });
  };

  const removeCond = (id) => {
    const updated = conditions.filter((c) => c.id !== id);
    updateNode({ ...node, data: { ...data, conditions: updated, defaultEdgeLabel } });
  };

  const setDefaultLabel = (val) => {
    updateNode({ ...node, data: { ...data, conditions, defaultEdgeLabel: val } });
  };

  return (
    <div className="p-3">
      <div className="d-flex justify-content-between align-items-center mb-2">
        <h5 className="fw-semibold mb-0">Conditions</h5>
        <button onClick={addCondition} className="btn btn-outline-secondary btn-sm">
          + Add
        </button>
      </div>

      {conditions.length === 0 && (
        <div className="small text-muted mb-2">No conditions yet — click “+ Add”</div>
      )}

      {conditions.map((c, idx) => (
        <div key={c.id || idx} className="p-2 mb-2 border rounded bg-white">
          <input
            className="form-control form-control-sm mb-2"
            value={c.label}
            onChange={(e) => updateCond(c.id, { label: e.target.value })}
            placeholder="Label (edge label)"
          />

          <div className="d-flex gap-2 mb-2">
            <input
              className="form-control form-control-sm"
              value={c.variable}
              onChange={(e) => updateCond(c.id, { variable: e.target.value })}
              placeholder="Variable (e.g. age or user.age)"
            />

            <select
              className="form-select form-select-sm"
              value={c.operator}
              onChange={(e) => updateCond(c.id, { operator: e.target.value })}
            >
              <option value="==">==</option>
              <option value="===">===</option>
              <option value="!=">!=</option>
              <option value="!==">!==</option>
              <option value=">">&gt;</option>
              <option value="<">&lt;</option>
              <option value=">=">&gt;=</option>
              <option value="<=">&lt;=</option>
              <option value="contains">contains</option>
              <option value="in">in</option>
            </select>

            <input
              className="form-control form-control-sm"
              style={{ maxWidth: "8rem" }}
              value={String(c.value ?? "")}
              onChange={(e) => updateCond(c.id, { value: e.target.value })}
              placeholder="value"
            />
          </div>

          <div className="d-flex justify-content-end">
            <button
              onClick={() => removeCond(c.id)}
              className="btn btn-link btn-sm text-danger p-0"
            >
              Delete
            </button>
          </div>
        </div>
      ))}

      <div className="mt-3">
        <label className="form-label small text-muted">Default edge label (fallback)</label>
        <input
          className="form-control form-control-sm mb-1"
          value={defaultEdgeLabel}
          onChange={(e) => setDefaultLabel(e.target.value)}
          placeholder="e.g. default"
        />
        <div className="small text-muted">
          The default edge label is used when no condition matches. You can also add a bottom handle
          named <code>out-default</code>.
        </div>
      </div>
    </div>
  );
}
