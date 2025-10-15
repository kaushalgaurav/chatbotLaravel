// src/utils/evaluate.js
export function getVar(vars = {}, name) {
  if (!name) return undefined;
  const parts = String(name).split(".");
  let cur = vars;
  for (const p of parts) {
    if (cur == null) return undefined;
    cur = cur[p];
  }
  return cur;
}

export function evaluateCondition(cond = {}, vars = {}) {
  const leftRaw = getVar(vars, cond.variable);
  const rightRaw = cond.value;

  // Attempt numeric coercion where sensible
  const leftNum = Number(leftRaw);
  const rightNum = Number(rightRaw);
  const left = !Number.isNaN(leftNum) && String(leftRaw).trim() !== "" ? leftNum : leftRaw;
  const right = !Number.isNaN(rightNum) && String(rightRaw).trim() !== "" ? rightNum : rightRaw;

  switch (cond.operator) {
    case "==": return left == right;
    case "===": return left === right;
    case "!=": return left != right;
    case "!==": return left !== right;
    case ">": return Number(left) > Number(right);
    case "<": return Number(left) < Number(right);
    case ">=": return Number(left) >= Number(right);
    case "<=": return Number(left) <= Number(right);
    case "contains": return String(left).includes(String(right));
    case "in": {
      try {
        const list = String(right).split(",").map(s => s.trim());
        return list.includes(String(left));
      } catch { return false; }
    }
    default: return false;
  }
}

/**
 * evaluateFormula
 * - Uses Function + `with(__vars)` to support referencing arbitrary variable names.
 * - Accepts `vars` object. It will only inject safe JS identifiers as direct args,
 *   the rest are reachable through __vars in the `with`.
 */
export function evaluateFormula(expression = "", vars = {}) {
  if (!expression) return undefined;
  try {
    const varNames = Object.keys(vars);
    const safeVarNames = varNames.filter(n => /^[$A-Z_a-z][0-9A-Z_a-z$]*$/.test(n));
    // Create a function where safe variables are direct params, and __vars provides full map.
    const fn = new Function(...safeVarNames, "__vars", `with(__vars){ return (${expression}); }`);
    const args = safeVarNames.map(k => vars[k]);
    return fn(...args, vars);
  } catch (err) {
    // Keep error visible for dev; caller can handle undefined
    // eslint-disable-next-line no-console
    console.error("Formula eval error:", err, "expression:", expression);
    return undefined;
  }
}

/** Simple template replacement: {varName} -> value, supports dot notation */
export function replaceTemplate(text, vars = {}) {
  if (typeof text !== "string") return text;
  return text.replace(/\{([\w$.]+)\}/g, (m, p1) => {
    const v = getVar(vars, p1);
    if (v === undefined || v === null) return "";
    if (typeof v === "object") return JSON.stringify(v);
    return String(v);
  });
}
