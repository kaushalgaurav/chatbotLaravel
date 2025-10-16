// src/components/NodeInspector.jsx
import React from "react";
import { useReactFlow } from "@xyflow/react";
import ConditionEditor from "./nodes/ConditionEditor";
import FormulaEditor from "./nodes/FormulaEditor";

// minimal: import quill css here if not imported globally
import "react-quill/dist/quill.snow.css";
import ReactQuill from "react-quill";

export default function NodeInspector({ node, onClose, updateNode: updateNodeProp }) {
  const { setNodes } = useReactFlow();

  const updateNode =
    updateNodeProp ||
    ((newNode) => {
      setNodes((nds) => nds.map((n) => (n.id === newNode.id ? newNode : n)));
    });

  if (!node) return null;

  // Quill config (small toolbar)
  const quillModules = {
    toolbar: [
      ["bold", "italic", "underline"],
      [{ header: [1, 2, false] }],
      [{ list: "ordered" }, { list: "bullet" }],
      ["link"],
      ["clean"],
    ],
  };
  const quillFormats = [
    "header",
    "bold",
    "italic",
    "underline",
    "list",
    "bullet",
    "link",
  ];

  // Helper: update a top-level data key
  const setDataKey = (key, value) => {
    updateNode({ ...node, data: { ...node.data, [key]: value } });
  };

  // Render Quill for array of strings (e.g., options)
  const renderArrayField = (key, arr) => {
    if (!Array.isArray(arr)) return null;
    return (
      <div className="mb-3" key={key}>
        <label className="form-label small text-muted">{key}</label>

        {arr.map((item, idx) => (
          <div
            key={idx}
            style={{
              marginBottom: 8,
              padding: 8,
              border: "1px solid #e9ecef",
              borderRadius: 6,
              background: "#fff",
            }}
          >
            <div style={{ display: "flex", justifyContent: "flex-end", gap: 8 }}>
              <button
                type="button"
                className="btn btn-sm btn-outline-danger"
                onClick={() => {
                  const copy = [...arr];
                  copy.splice(idx, 1);
                  setDataKey(key, copy);
                }}
                title="Remove option"
              >
                &times;
              </button>
            </div>

            <ReactQuill
              theme="snow"
              modules={quillModules}
              formats={quillFormats}
              value={item || ""}
              onChange={(html) => {
                const copy = [...arr];
                copy[idx] = html;
                setDataKey(key, copy);
              }}
              style={{ minHeight: 80, marginTop: 8 }}
            />
          </div>
        ))}

        <div style={{ marginTop: 8 }}>
          <button
            type="button"
            className="btn btn-sm btn-outline-primary"
            onClick={() => {
              const copy = Array.isArray(arr) ? [...arr] : [];
              copy.push(""); // new empty option (Quill will show empty editor)
              setDataKey(key, copy);
            }}
          >
            + Add option
          </button>
        </div>
      </div>
    );
  };

  // Render all other string fields as Quill (except reserved keys and keys ending with "Html")
  const renderGenericStringFields = () => {
    if (!node.data) return null;
    const skipKeys = new Set(["varName", "logicType"]); // keep varName plain, skip internal flags
    const htmlSuffixRegex = /html$/i;

    return Object.keys(node.data).map((key) => {
      if (skipKeys.has(key)) return null;
      if (htmlSuffixRegex.test(key)) return null; // skip *Html keys
      const val = node.data[key];

      // We'll handle message 'text' and question 'label' and arrays explicitly elsewhere
      if (key === "text" || key === "label" || Array.isArray(val)) return null;

      if (typeof val === "string") {
        return (
          <div className="mb-3" key={key}>
            <label className="form-label small text-muted">{key}</label>
            <ReactQuill
              theme="snow"
              modules={quillModules}
              formats={quillFormats}
              value={val || ""}
              onChange={(html) => setDataKey(key, html)}
              style={{ minHeight: 80 }}
            />
          </div>
        );
      }
      return null;
    });
  };

  // helper to render array fields for any arrays-of-strings except keys we already handled and keys ending with Html
  const renderOtherArrays = () => {
    if (!node.data) return null;
    const htmlSuffixRegex = /html$/i;
    const handledKeys = new Set(["options", "text", "label"]); // keys already rendered explicitly

    return Object.keys(node.data).map((k) => {
      if (htmlSuffixRegex.test(k)) return null;
      if (handledKeys.has(k)) return null; // avoid duplicates
      const v = node.data[k];
      if (Array.isArray(v) && v.every((it) => typeof it === "string")) {
        return renderArrayField(k, v);
      }
      return null;
    });
  };

  return (
    <div
      className="position-fixed bg-white shadow overflow-auto"
      style={{
        left: 0,
        top: "4rem",
        width: "24rem",
        height: "calc(100vh - 4rem)",
        zIndex: 50,
      }}
    >
      <div className="p-3 border-bottom d-flex justify-content-between align-items-start">
        <div>
          <div className="fw-semibold">{node.type} node</div>
          <div className="small text-muted">id: {node.id}</div>
        </div>
        <div>
          <button onClick={onClose} className="btn btn-outline-secondary btn-sm">
            Close
          </button>
        </div>
      </div>

      <div className="p-3">
        {/* MESSAGE node: keep backwards-compatible key `text` */}
        {node.type === "message" && (
          <>
            <label className="form-label small text-muted">Message</label>
            <div style={{ marginBottom: "0.75rem" }}>
              <ReactQuill
                theme="snow"
                modules={quillModules}
                formats={quillFormats}
                value={node.data?.text || ""}
                onChange={(html) =>
                  updateNode({ ...node, data: { ...node.data, text: html } })
                }
                style={{ minHeight: 120 }}
              />
            </div>
          </>
        )}

        {/* QUESTION node: label -> rich, varName -> plain */}
        {node.type === "question" && (
          <>
            <label className="form-label small text-muted">Question</label>
            <div className="mb-2">
              <ReactQuill
                theme="snow"
                modules={quillModules}
                formats={quillFormats}
                value={node.data?.label || ""}
                onChange={(html) =>
                  updateNode({ ...node, data: { ...node.data, label: html } })
                }
                style={{ minHeight: 80 }}
              />
            </div>

            <label className="form-label small text-muted">Variable name</label>
            <input
              className="form-control mb-2"
              value={node.data?.varName || ""}
              onChange={(e) =>
                updateNode({ ...node, data: { ...node.data, varName: e.target.value } })
              }
            />
          </>
        )}

        {/* BUTTONS / options: commonly stored in node.data.options (array of strings) */}
        {Array.isArray(node.data?.options) && renderArrayField("options", node.data.options)} 

        {/* Generic other arrays-of-strings (skipping *Html keys and handled keys) */}
        {renderOtherArrays()}

        {/* Other arbitrary string fields (skipping *Html keys) */}
        {renderGenericStringFields()}

        {/* Legacy / special editors */}
        {(node.type === "condition" ||
          (node.type === "logic" && node.data?.logicType === "condition")) && (
          <ConditionEditor node={node} updateNode={updateNode} />
        )}

        {(node.type === "formula" ||
          (node.type === "logic" && node.data?.logicType === "formula")) && (
          <FormulaEditor node={node} updateNode={updateNode} />
        )}
      </div>
    </div>
  );
}
