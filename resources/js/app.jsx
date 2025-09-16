// // resources/js/landbot/App.jsx
// import React from "react";                 // <<--- ADD THIS
// import { ReactFlowProvider } from "@xyflow/react";
// import { BrowserRouter, Routes, Route } from "react-router-dom";

// import FlowApp from "./landbot/components/FlowApp";
// import SharePage from "./landbot/pages/SharePage";
// import PreviewPage from "./landbot/pages/PreviewPage";

// export default function App() {
//   return (
//     <BrowserRouter>
//       <ReactFlowProvider>
//         <Routes>
//           {/* Main flow builder */}
//           <Route path="/" element={<FlowApp />} />
//           {/* Share page */}
//           <Route path="/share" element={<SharePage />} />
//           <Route path="/bot/preview/:botId" element={<PreviewPage />} />
//         </Routes>
//       </ReactFlowProvider>
//     </BrowserRouter>
//   );
// }
// // src/App.jsx
// import { ReactFlowProvider } from "@xyflow/react";
// import { BrowserRouter, Routes, Route } from "react-router-dom";
// import { FlowApp, SharePage, PreviewPage} from "./components/index";

// export default function App() {
//   return (
//     <BrowserRouter>
//       <ReactFlowProvider>
//         <Routes>
//           {/* Main flow builder */}
//           <Route path="/" element={<FlowApp />} />

//           {/* Share page */}
//           <Route path="/share" element={<SharePage />} />
//            <Route path="/bot/preview/:botId" element={<PreviewPage />} />
//         </Routes>
//       </ReactFlowProvider>
//     </BrowserRouter>
//   );
// }



// import { BrowserRouter, Routes, Route } from "react-router-dom";
// import { FlowApp, SharePage, PreviewPage } from "./components/index";

// export default function App(){
//   return (
//     <BrowserRouter basename="/chatbots/build">
//       <Routes>
//         <Route path="/" element={<FlowApp/>} />
//         <Route path="/share" element={<SharePage/>} />
//         <Route path="/bot/preview/:botId" element={<PreviewPage/>} />
//       </Routes>
//     </BrowserRouter>
//   );
// }

import { ReactFlowProvider } from "@xyflow/react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { FlowApp, SharePage, PreviewPage } from "./landbot/components/index";

export default function App() {
  return (
    <BrowserRouter basename="/chatbots">
      <ReactFlowProvider>
        <Routes>
          {/* /chatbots -> FlowApp (list or main builder index) */}
          <Route path="/" element={<FlowApp />} />

          {/* /chatbots/share */}
          <Route path="share" element={<SharePage />} />

          {/* /chatbots/bot/preview/:botId  (if you want to keep preview route under /chatbots/bot/preview/...) */}
          <Route path="bot/preview/:botId" element={<PreviewPage />} />

          {/* /chatbots/:id/build  <-- THIS matches Laravel /chatbots/{encrypted}/build */}
          <Route path=":id/build" element={<FlowApp />} />
        </Routes>
      </ReactFlowProvider>
    </BrowserRouter>
  );
}
