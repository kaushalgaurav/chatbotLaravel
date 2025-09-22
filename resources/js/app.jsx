import { ReactFlowProvider } from "@xyflow/react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { FlowApp, SharePage, PreviewPage } from "./landbot/components/index";
// import './bootstrap';    // if you use this (axios/csrf)
import './app';   

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
