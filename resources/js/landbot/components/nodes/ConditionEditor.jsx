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
      <div className="flex justify-between items-center">
        <h3 className="font-semibold">Conditions</h3>
        <button onClick={addCondition} className="text-sm px-2 py-1 border rounded">
          + Add
        </button>
      </div>

      <div className="mt-2 space-y-2">
        {conditions.length === 0 && <div className="text-xs text-gray-500">No conditions yet — click “+ Add”</div>}

        {conditions.map((c, idx) => (
          <div key={c.id || idx} className="p-2 border rounded bg-white">
            <input
              className="w-full mb-1 text-sm p-1 border rounded"
              value={c.label}
              onChange={(e) => updateCond(c.id, { label: e.target.value })}
              placeholder="Label (edge label)"
            />

            <div className="flex gap-2">
              <input
                className="flex-1 p-1 border rounded text-sm"
                value={c.variable}
                onChange={(e) => updateCond(c.id, { variable: e.target.value })}
                placeholder="Variable (e.g. age or user.age)"
              />

              <select
                className="p-1 border rounded text-sm"
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
                className="w-32 p-1 border rounded text-sm"
                value={String(c.value ?? "")}
                onChange={(e) => updateCond(c.id, { value: e.target.value })}
                placeholder="value"
              />
            </div>

            <div className="flex justify-end mt-1">
              <button onClick={() => removeCond(c.id)} className="text-xs text-red-600">
                Delete
              </button>
            </div>
          </div>
        ))}

        <div className="mt-3">
          <label className="text-xs text-gray-600">Default edge label (fallback)</label>
          <input
            className="w-full mt-1 p-1 border rounded text-sm"
            value={defaultEdgeLabel}
            onChange={(e) => setDefaultLabel(e.target.value)}
            placeholder="e.g. default"
          />
          <div className="text-xs text-gray-400 mt-1">
            The default edge label is used when no condition matches. You can also add a bottom handle named <code>out-default</code>.
          </div>
        </div>
      </div>
    </div>
  );
}
