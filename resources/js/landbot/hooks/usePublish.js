// src/hooks/usePublish.js
import { useCallback, useState } from "react";

const PUBLISH_KEY = "published-flow:v1";
const MAX_VERSIONS = 10;

export default function usePublish(getFlowSnapshot, options = {}) {
  const { apiUrl = null, apiAuth = null, sendBeforeSave = false } = options;

  const [publishing, setPublishing] = useState(false);
  const [toast, setToast] = useState(null);

  // 🔹 Read chatbotId and userId from <div id="root" ...>
  const rootEl = document.getElementById("root");
  const chatbotId = rootEl?.dataset?.chatbotId || null;
  const userId = rootEl?.dataset?.userId || null;

  const validateFlow = useCallback((flowObj) => {
    if (!flowObj) return { ok: false, reason: "Empty flow" };
    const nodeCount = (flowObj.nodes && flowObj.nodes.length) || 0;
    if (nodeCount === 0)
      return { ok: false, reason: "Add at least one node before publishing." };
    return { ok: true };
  }, []);

  // inside usePublish.js
  const sendToApi = useCallback(async (versionPayload) => {
    if (!apiUrl) return { ok: true, skip: true };

    // 🔹 read CSRF token from meta tag
    const csrfToken = document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content");

    try {
      const resp = await fetch(apiUrl, {
        method: "POST",
        credentials: "include", // include cookies/session
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {}), // 🔹 attach CSRF token
          ...(apiAuth ? { Authorization: apiAuth } : {}),
        },
        body: JSON.stringify(versionPayload),
      });

      const text = await resp.text().catch(() => null);
      let data = null;
      try {
        data = text ? JSON.parse(text) : null;
      } catch (e) {
        data = null;
      }

      if (!resp.ok) {
        return {
          ok: false,
          status: resp.status,
          message:
            (data && (data.message || data.error)) ||
            text ||
            `HTTP ${resp.status}`,
        };
      }

      return { ok: true, data };
    } catch (err) {
      return { ok: false, error: err.message || String(err) };
    }
  }, [apiUrl, apiAuth]);


  const publish = useCallback(async () => {
    setPublishing(true);
    try {
      const flowSnapshot = getFlowSnapshot();
      const validated = validateFlow(flowSnapshot);
      if (!validated.ok) {
        setToast({ type: "error", message: validated.reason });
        setPublishing(false);
        return;
      }

      const now = Date.now();
      const iso = new Date(now).toISOString();

      // 🔹 Add chatbotId + userId into payload
      // inside publish(), replace version with this:

      const chatbotIdRaw = chatbotId ?? ""; // string from dataset
      const userIdRaw = userId ?? "";

      const version = {
        id: `v-${now}`,
        ts: now,
        date: iso,

        // bot/user ids as strings (server wants bot_id string)
        bot_id: String(chatbotIdRaw),
        chatbot_id: String(chatbotIdRaw),
        botId: String(chatbotIdRaw),
        chatbotId: String(chatbotIdRaw),

        user_id: String(userIdRaw),
        userId: String(userIdRaw),

        // summary + flow object
        summary: {
          nodeCount: (flowSnapshot.nodes || []).length,
          edgeCount: (flowSnapshot.edges || []).length,
        },
        flow: flowSnapshot,

        // server expects 'json' to be an array -> use nodes array (common expectation)
        json: Array.isArray(flowSnapshot.nodes) ? flowSnapshot.nodes : [],

        // safe fallbacks / aliases
        json_full: flowSnapshot,            // entire object in case they want it
        json_string: JSON.stringify(flowSnapshot), // string fallback
        flow_json: JSON.stringify(flowSnapshot),

        __diagnostic: { payloadSentAt: iso, origin: typeof window !== "undefined" ? window.location.origin : null }
      };


      // Local save (always)
      const publishedRaw = localStorage.getItem(PUBLISH_KEY);
      let published = publishedRaw ? JSON.parse(publishedRaw) : { versions: [] };
      published.versions.push(version);
      if (published.versions.length > MAX_VERSIONS) {
        published.versions = published.versions.slice(-MAX_VERSIONS);
      }
      localStorage.setItem(PUBLISH_KEY, JSON.stringify(published));

      // Remote save (if API configured)
      if (apiUrl) {
        const apiResult = await sendToApi(version);
        if (!apiResult.ok) {
          const msg = apiResult.message || apiResult.error || "Remote publish failed";
          setToast({ type: "error", message: `Remote publish failed: ${msg}. Saved locally.` });
        } else {
          setToast({ type: "success", message: "Bot published successfully (remote)." });
        }
      } else {
        setToast({ type: "success", message: "Bot published locally." });
      }

      // Notify other windows/components
      try {
        window.dispatchEvent(
          new CustomEvent("botPublished", { detail: { latestId: version.id } })
        );
      } catch (e) {
        // ignore
      }

      setPublishing(false);
    } catch (err) {
      console.error("Publish failed", err);
      setToast({ type: "error", message: "Publish failed. Check console." });
      setPublishing(false);
    }
  }, [getFlowSnapshot, validateFlow, apiUrl, sendToApi, chatbotId, userId]);

  const clearToast = useCallback(() => setToast(null), []);

  return {
    publishing,
    toast,
    publish,
    clearToast,
  };
}
