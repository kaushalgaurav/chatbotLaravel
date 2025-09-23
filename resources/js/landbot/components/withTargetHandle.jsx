// resources/js/landbot/components/withTargetHandle.jsx
import React from "react";
import { Handle, Position } from "@xyflow/react";

/**
 * HOC that wraps a node component and injects a left-side target handle.
 * Usage: withTargetHandle(MyNode)
 */
export default function withTargetHandle(NodeComponent) {
  return function WrappedNode(props) {
    return (
      <div className="position-relative" style={{ width: "100%" }}>
        <NodeComponent {...props} />
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
          }}
        />
      </div>
    );
  };
}
