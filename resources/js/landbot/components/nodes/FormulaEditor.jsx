// src/components/nodes/FormulaEditor.jsx
import React from "react";

export default function FormulaEditor({ node, updateNode }) {
  const data = node.data || {};
  const formula = data.formula || { expression: "", outputVar: "" };

  const setExpr = (expr) =>
    updateNode({ ...node, data: { ...data, formula: { ...formula, expression: expr } } });

  const setOut = (out) =>
    updateNode({ ...node, data: { ...data, formula: { ...formula, outputVar: out } } });

  return (
    <div className="p-3">
      <h5 className="fw-semibold mb-3">Formula</h5>

      <div className="mb-3">
        <label className="form-label small text-muted">Expression</label>
        <textarea
          rows={3}
          className="form-control form-control-sm"
          value={formula.expression}
          onChange={(e) => setExpr(e.target.value)}
          placeholder="e.g. parseFloat(score1) + parseFloat(score2) — use parseFloat() for numeric addition"
        />
        <div className="form-text small text-muted mt-1">
          You can reference variables by name (e.g. <code>score1</code>). Use{" "}
          <code>parseFloat(var)</code> or <code>Number(var)</code> to coerce strings to numbers.
        </div>
      </div>

      <div>
        <label className="form-label small text-muted">Output variable</label>
        <input
          className="form-control form-control-sm"
          value={formula.outputVar}
          onChange={(e) => setOut(e.target.value)}
          placeholder="e.g. totalScore"
        />
        <div className="form-text small text-muted mt-1">
          The expression result will be stored in this variable (accessible as{" "}
          <code>{`{variableName}`}</code> in messages).
        </div>
      </div>
    </div>
  );
}
