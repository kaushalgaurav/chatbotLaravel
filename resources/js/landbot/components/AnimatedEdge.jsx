// resources/js/landbot/components/AnimatedEdge.jsx
import React, { useState } from "react";
import { getSmoothStepPath } from "@xyflow/react";

export default function AnimatedEdge({
    id,
    sourceX, sourceY, sourcePosition,
    targetX, targetY, targetPosition,
    markerEnd
}) {
    const [hover, setHover] = useState(false);

    const [path] = getSmoothStepPath({
        sourceX, sourceY, sourcePosition,
        targetX, targetY, targetPosition
    });

    return (
        <g onMouseEnter={() => setHover(true)} onMouseLeave={() => setHover(false)}>
            <defs>
                <marker
                    id={`arrow-${id}`}
                    markerWidth="6"      // smaller overall size
                    markerHeight="6"
                    refX="6"             // aligns arrow tip at edge end
                    refY="3"             // vertical center
                    orient="auto"
                >
                    {/* slimmer, sharper arrow */}
                    <path d="M0,0 L6,3 L0,6 Z" fill="#49b8a6" />
                </marker>

            </defs>

            <path
                id={id}
                d={path}
                className={`animated-edge ${hover ? "hover" : ""}`}
                fill="none"
                strokeWidth={3}
                markerEnd={`url(#arrow-${id})`}
            />
        </g>
    );
}
