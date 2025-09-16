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
      <h3 className="font-semibold mb-2">Formula</h3>

      <div>
        <label className="text-xs text-gray-600">Expression</label>
        <textarea
          rows={3}
          className="w-full p-2 border rounded text-sm"
          value={formula.expression}
          onChange={(e) => setExpr(e.target.value)}
          placeholder="e.g. parseFloat(score1) + parseFloat(score2)  — use parseFloat() for numeric addition"
        />
        <div className="text-xs text-gray-400 mt-1">
          You can reference variables by name (e.g. <code>score1</code>). Use <code>parseFloat(var)</code> or <code>Number(var)</code> to coerce strings to numbers.
        </div>
      </div>

      <div className="mt-2">
        <label className="text-xs text-gray-600">Output variable</label>
        <input
          className="w-full p-1 border rounded text-sm"
          value={formula.outputVar}
          onChange={(e) => setOut(e.target.value)}
          placeholder="e.g. totalScore"
        />
        <div className="text-xs text-gray-400 mt-1">
          The expression result will be stored in this variable (accessible as <code>{`{variableName}`}</code> in messages).
        </div>
      </div>
    </div>
  );
}
