// resources/js/landbot/components/withHandles.jsx
import React from "react";
import { Handle, Position } from "@xyflow/react";

export default function withHandles(NodeComponent) {
  return function WrappedNode(props) {
    return (
      <div className="position-relative" style={{ width: "100%" }}>
        <NodeComponent {...props} />

        {/* LEFT target (visible) */}
        <Handle
          type="target"
          id="in"
          position={Position.Left}
          style={{
            background: "#49b8a6",
            border: "2px solid #fff",   
            width: 12,
            height: 12,
            left: -6,
            top: "50%",
            transform: "translateY(-50%)",
            borderRadius: "50%",
            boxShadow: "0 4px 10px rgba(20,30,40,0.12)",
            zIndex: 5,
          }}
        />

        {/* RIGHT source (invisible but interactive) */}
        <Handle
          type="source"
          id="arrow"
          position={Position.Right}
          style={{
            opacity: 0,
            width: 20,
            height: 20,
            right: -10,
            top: "50%",
            transform: "translateY(-50%)",
            pointerEvents: "auto",
            zIndex: 5,
          }}
        />
      </div>
    );
  };
}
