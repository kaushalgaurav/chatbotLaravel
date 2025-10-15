import React from "react";

const AddButton = React.memo(function AddButton({ id, onAdd }) {
  const handleClick = (e) => {
    e.stopPropagation();
    if (typeof onAdd === "function") onAdd(id, e);
    console.log("clicked", id);
  };

  return (
    <button
      // prevent canvas drag/selection from starting on mousedown
      onMouseDown={(e) => {
        e.stopPropagation();
        // leave focus behavior for keyboard accessibility
      }}
      onClick={handleClick}
      aria-label="Add next node"
      title="Add next node"
      className="btn btn-sm rounded-circle shadow position-absolute"
      style={{
        right: "-12px",
        top: "50%",
        transform: "translateY(-50%)",
        width: "20px",
        height: "20px",
        fontSize: "12px",
        lineHeight: "1",
        padding: "0",
        backgroundColor: "#49b8a6",
        border: "none",
        zIndex: 9999,         // make this very high so it sits above the handle
        cursor: "pointer",
        pointerEvents: "auto",
      }}
    >
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5 12H19" stroke="white" strokeWidth="2" strokeLinecap="round" />
        <path d="M13 6L19 12L13 18" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
    </button>
  );
});

export default AddButton;
