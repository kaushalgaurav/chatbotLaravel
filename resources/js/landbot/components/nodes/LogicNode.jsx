// src/components/nodes/LogicNode.jsx
import { Handle, Position } from "@xyflow/react";
import { AddButton, KebabMenu} from "../index";

export default function LogicNode({ data = {}, id }) {
  // data expected shape:
  // data.logicType: 'condition' | 'formula'
  // data.conditions: [{ id, label, variable, operator, value }]
  // data.formula: { expression, outputVar }
  const conditions = data.conditions || [];
  const formula = data.formula;

  return (
    <div className="bg-white border-l-4 border-indigo-500 rounded-lg p-3 shadow w-50 relative">
      <KebabMenu onDelete={data?.onDelete} />
      <AddButton id={id} onAdd={data?.onAddClick} />
      <div className="flex items-center justify-between">
        <p className="font-semibold">🧠 Logic</p>
        <div className="text-xs text-gray-500">{data.logicType || "condition"}</div>
      </div>

      <div className="mt-2 text-sm">
        {data.logicType === "condition" && (
          <>
            <div className="text-xs text-gray-600 mb-1">Conditions</div>
            <ul className="space-y-1">
              {conditions.slice(0, 3).map((c) => (
                <li key={c.id} className="text-sm">
                  <strong className="mr-1">{c.label}</strong>
                  <span className="text-gray-600">
                    {c.variable} {c.operator} {c.value}
                  </span>
                </li>
              ))}
              {conditions.length === 0 && (
                <li className="text-xs text-gray-400">No conditions configured</li>
              )}
              {conditions.length > 3 && (
                <li className="text-xs text-gray-400">+{conditions.length - 3} more</li>
              )}
            </ul>
          </>
        )}

        {data.logicType === "formula" && formula && (
          <>
            <div className="text-xs text-gray-600 mb-1">Formula</div>
            <div className="text-sm break-words">{formula.expression}</div>
            <div className="text-xs text-gray-400 mt-1">→ {formula.outputVar || "(no output var)"}</div>
          </>
        )}
      </div>

      {/* Input handle */}
      <Handle type="target" position={Position.Top} className="!bg-gray-500" id="in" />

      {/* Outgoing handles for conditions (right side) */}
      {data.logicType === "condition" &&
        conditions.map((c, i) => (
          <Handle
            key={c.id}
            type="source"
            position={Position.Right}
            id={`out-${c.label || c.id}`}
            style={{ top: 60 + i * 20, background: "#10B981" }}
            className="!bg-green-500"
          />
        ))}

      {/* Default/fallback handle */}
      {data.logicType === "condition" && (
        <Handle
          type="source"
          position={Position.Bottom}
          id="out-default"
          style={{ background: "#94a3b8" }}
          className="!bg-gray-400"
        />
      )}

      {/* Single outgoing handle for formula (bottom) */}
      {data.logicType === "formula" && (
        <Handle type="source" position={Position.Bottom} id="out" className="!bg-gray-500" />
      )}
    </div>
  );
}
