// src/pages/PreviewPage.jsx
import React, { useEffect, useRef, useState } from "react";
import { useParams, useLocation } from "react-router-dom";
// <-- added import (will work if your components export Chatbot)
import Chatbot from "../components/Chatbot";

/**
 * PreviewPage
 * - Reads :botId from URL
 * - Attempts to load published snapshot from a few localStorage keys:
 *   1) published-flow:v1 (versions array)
 *   2) published-bot:<botId>
 *   3) published-bots-index -> last
 * - Renders either your real Chatbot component (if available) or the lightweight chat UI fallback.
 * - Listens for postMessage({type:'prefill', data}) and displays received prefill
 */

// Tweak this value to control how wide the preview should be
const PREVIEW_MAX_WIDTH = 900; // px — change to 1200 or '100%' as needed

function findPublishedById(botId) {
  if (!botId) return null;

  try {
    // 1) published-flow:v1 (your original key)
    const raw = localStorage.getItem("published-flow:v1");
    if (raw) {
      const parsed = JSON.parse(raw);
      const versions = parsed?.versions;
      if (Array.isArray(versions)) {
        const found = versions.find((v) => v.id === botId);
        if (found) return found.flow || found;
      }
    }
  } catch (e) {
    /* ignore parse errors */
  }

  try {
    // 2) published-bot:<botId>
    const raw2 = localStorage.getItem(`published-bot:${botId}`);
    if (raw2) {
      const parsed = JSON.parse(raw2);
      // record may be { snapshot, meta, ... } or direct flow
      return parsed.snapshot || parsed.flow || parsed;
    }
  } catch (e) {}

  try {
    // 3) index fallback -> published-bots-index (if user used earlier demo code)
    const idxRaw = localStorage.getItem("published-bots-index");
    if (idxRaw) {
      const idx = JSON.parse(idxRaw);
      if (Array.isArray(idx) && idx.length) {
        const pick = idx.includes(botId) ? botId : idx[idx.length - 1];
        const raw3 = localStorage.getItem(`published-bot:${pick}`);
        if (raw3) {
          const parsed = JSON.parse(raw3);
          return parsed.snapshot || parsed.flow || parsed;
        }
      }
    }
  } catch (e) {}

  // Nothing found
  return null;
}

function inferBotIntroMessages(flow) {
  // Try to extract a few textual messages from flow nodes.
  if (!flow) return [];
  const nodes = Array.isArray(flow.nodes) ? flow.nodes : [];

  // Common node shapes: type 'message' with data.text OR data.label
  const messages = [];
  for (const n of nodes) {
    const t = (n.type || "").toLowerCase();
    const text =
      (n.data && (n.data.text || n.data.label || n.data.message)) || null;
    if (text && messages.length < 5) {
      messages.push({ id: n.id || `n-${messages.length}`, text });
    } else if (!text && (t.includes("msg") || t.includes("message"))) {
      // sometimes message nodes store data differently
      messages.push({ id: n.id || `n-${messages.length}`, text: "[message]" });
    }
    if (messages.length >= 5) break;
  }

  // If nothing found, try flow.meta.description/title
  if (!messages.length && flow.meta) {
    if (flow.meta.title) messages.push({ id: "meta-title", text: flow.meta.title });
    if (flow.meta.description) messages.push({ id: "meta-desc", text: flow.meta.description });
  }

  return messages;
}

export default function PreviewPage() {
  const { botId } = useParams();
  const location = useLocation();
  const [flow, setFlow] = useState(null);
  const [error, setError] = useState("");
  const [messages, setMessages] = useState([]); // { from: 'bot'|'user', text }
  const [input, setInput] = useState("");
  const containerRef = useRef(null);

  useEffect(() => {
    if (!botId) {
      setError("Missing botId in URL.");
      return;
    }

    const published = findPublishedById(botId);
    if (!published) {
      setError("Published flow not found. Publish from the Share page first.");
      return;
    }

    setFlow(published);

    // infer intro bot messages and push to UI (fallback only)
    const intro = inferBotIntroMessages(published);
    if (intro.length) {
      const introMsgs = intro.map((m) => ({ from: "bot", text: m.text }));
      setMessages((s) => [...introMsgs, ...s]);
    } else {
      // default greeting fallback
      setMessages((s) => [
        ...s,
        { from: "bot", text: "Hello — this bot has no intro messages configured." },
      ]);
    }
  }, [botId]);

  useEffect(() => {
    // Listen for postMessage prefill
    function onMessage(evt) {
      // In production verify evt.origin === allowed origin
      const payload = evt.data || {};
      if (payload && payload.type === "prefill" && payload.data) {
        // if Chatbot component exposes a window method, try to call it
        try {
          if (window.chatRuntime && typeof window.chatRuntime.prefill === "function") {
            window.chatRuntime.prefill(payload.data);
            return;
          }
        } catch (e) {
          // ignore
        }

        // fallback: show a small message to the user with prefill content
        setMessages((s) => [
          ...s,
          { from: "bot", text: "Prefill received:" },
          { from: "bot", text: JSON.stringify(payload.data) },
        ]);
      }
    }
    window.addEventListener("message", onMessage);
    return () => window.removeEventListener("message", onMessage);
  }, []);

  const sendMessage = (txt) => {
    if (!txt || !txt.trim()) return;
    const userMsg = { from: "user", text: txt.trim() };
    setMessages((s) => [...s, userMsg]);
    setInput("");

    // naive echo/responder for demo: respond with canned reply or lookup nodes
    setTimeout(() => {
      const nodes = Array.isArray(flow?.nodes) ? flow.nodes : [];
      let reply = null;
      const lower = txt.toLowerCase();
      for (const n of nodes) {
        const text = (n.data && (n.data.text || n.data.label || n.data.message)) || "";
        if (text && typeof text === "string" && text.toLowerCase().includes(lower)) {
          reply = text;
          break;
        }
      }
      if (!reply) {
        reply = "Thanks — I got your message. (This is a demo reply.)";
      }
      setMessages((s) => [...s, { from: "bot", text: reply }]);
    }, 500);
  };

  if (error) {
    return (
      <div style={{ padding: 20, fontFamily: "Inter, Arial, sans-serif" }}>
        <h3>Bot preview</h3>
        <div style={{ color: "red" }}>{error}</div>
        <div style={{ marginTop: 12 }}>
          Open <code>/share</code> and click Publish to create a published bot for preview.
        </div>
      </div>
    );
  }

  // If a Chatbot component exists and flow is loaded, render it (preferred)
  // --- TRY TO MOUNT REAL Chatbot (robust with logging & fallbacks) ---
  if (flow) {
    try {
      // If Chatbot is available as a component, try common prop shapes.
      if (typeof Chatbot === "function" || typeof Chatbot === "object") {
        // Wrap the Chatbot in a wider centered container
        const wrapperStyle = {
          padding: 12,
          minHeight: 320,
          boxSizing: "border-box",
          maxWidth: PREVIEW_MAX_WIDTH,
          margin: "0 auto",
          width: "100%",
        };

        // 1) Try passing full 'flow' object (some runtimes expect this)
        try {
          return (
            <div style={wrapperStyle}>
              <Chatbot flow={flow} nodes={flow.nodes || []} edges={flow.edges || []} onClose={() => {}} />
            </div>
          );
        } catch (errFlow) {
          console.warn("Chatbot(flow) mount failed — trying nodes/edges shape", errFlow);
        }

        // 2) Fallback: try nodes/edges props
        try {
          return (
            <div style={wrapperStyle}>
              <Chatbot nodes={flow.nodes || []} edges={flow.edges || []} onClose={() => {}} />
            </div>
          );
        } catch (errNE) {
          console.warn("Chatbot(nodes,edges) mount failed — will fallback to demo UI", errNE);
        }
      } else {
        console.warn("Chatbot import is not a component (value):", Chatbot);
      }
    } catch (outerErr) {
      console.error("Error while attempting to mount Chatbot:", outerErr);
    }
    // If mounting fails, execution falls through to the demo UI below.
  }

  // fallback: lightweight preview UI (keeps previous demo behavior)
  return (
    <div
      ref={containerRef}
      style={{
        minHeight: 320,
        fontFamily: "Inter, Arial, sans-serif",
        padding: 12,
        boxSizing: "border-box",
        display: "flex",
        justifyContent: "center",
      }}
    >
      <div style={{ width: "100%", maxWidth: PREVIEW_MAX_WIDTH }}>
        <div style={{ padding: 12, background: "#fff", borderRadius: 8, boxShadow: "0 4px 18px rgba(0,0,0,0.06)" }}>
          <div style={{ fontWeight: 700, marginBottom: 8 }}>
            {flow?.meta?.title || "Published bot"}
          </div>

          <div style={{ height: 420, overflow: "auto", padding: 8, border: "1px solid #eee", borderRadius: 6, background: "#fafafa" }}>
            {messages.length === 0 && (
              <div style={{ color: "#888", fontSize: 13 }}>No messages yet.</div>
            )}
            {messages.map((m, i) => (
              <div key={i} style={{ display: "flex", marginBottom: 8, justifyContent: m.from === "user" ? "flex-end" : "flex-start" }}>
                <div
                  style={{
                    maxWidth: "86%",
                    padding: "8px 10px",
                    borderRadius: 10,
                    background: m.from === "user" ? "#3b82f6" : "#fff",
                    color: m.from === "user" ? "#fff" : "#111",
                    boxShadow: m.from === "user" ? "none" : "0 2px 8px rgba(0,0,0,0.04)",
                    fontSize: 14,
                  }}
                >
                  {typeof m.text === "string" ? m.text : JSON.stringify(m.text)}
                </div>
              </div>
            ))}
          </div>

          <div style={{ marginTop: 10, display: "flex", gap: 8 }}>
            <input
              value={input}
              onChange={(e) => setInput(e.target.value)}
              onKeyDown={(e) => { if (e.key === "Enter") sendMessage(input); }}
              placeholder="Type a message..."
              style={{ flex: 1, padding: "8px 10px", borderRadius: 8, border: "1px solid #ddd" }}
            />
            <button onClick={() => sendMessage(input)} style={{ padding: "8px 12px", borderRadius: 8, border: "none", background: "#111827", color: "#fff" }}>
              Send
            </button>
          </div>

          <div style={{ marginTop: 8, fontSize: 12, color: "#666" }}>
            This is a lightweight preview runtime. Replace this with your real chat runtime mounting code.
          </div>
        </div>
      </div>
    </div>
  );
}
