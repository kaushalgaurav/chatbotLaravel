import React from "react";

const AddButton = React.memo(function AddButton({ id, onAdd }) {
  const handleClick = (e) => {
    e.stopPropagation();
    if (typeof onAdd === "function") onAdd(id, e);
  };

  return (
    <button
      onClick={handleClick}
      aria-label="Add next node"
      title="Add next node"
      className="btn btn-primary btn-sm rounded-circle shadow position-absolute"
      style={{
        right: "-12px",       // equivalent to Tailwind -right-3
        top: "50%",           // top-1/2
        transform: "translateY(-50%)", // -translate-y-1/2
        width: "20px",        // w-6
        height: "20px",       // h-6
        fontSize: "12px",     // text-xs
        lineHeight: "1",      // ensure icon centers nicely
        padding: "0"          // compact button
      }}
    >
       <svg
        width="14"
        height="14"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M5 12H19"
          stroke="white"
          strokeWidth="2"
          strokeLinecap="round"
        />
        <path
          d="M13 6L19 12L13 18"
          stroke="white"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
        />
      </svg>
    </button>
  );
});

export default AddButton;






