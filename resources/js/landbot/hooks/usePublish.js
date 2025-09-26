// src/hooks/usePublish.js
import { useCallback, useState } from "react";

const PUBLISH_KEY = "published-flow:v1";
const MAX_VERSIONS = 10;

export default function usePublish(getFlowSnapshot, options = {}) {
  const { apiUrl = null, apiAuth = null } = options;
  const [publishing, setPublishing] = useState(false);
  const [toast, setToast] = useState(null);

  const validateFlow = useCallback((f) => {
    if (!f) return { ok: false, reason: "Empty flow" };
    if (!Array.isArray(f.nodes) || f.nodes.length === 0)
      return { ok: false, reason: "Add at least one node before publishing." };
    return { ok: true };
  }, []);

  const sendToApi = useCallback(
    async (payload) => {
      if (!apiUrl) return { ok: true, skip: true };
      const csrf = typeof document !== "undefined"
        ? document.querySelector('meta[name="csrf-token"]')?.getAttribute("content")
        : null;

      try {
        const res = await fetch(apiUrl, {
          method: "POST",
          credentials: "include",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            ...(csrf ? { "X-CSRF-TOKEN": csrf } : {}),
            ...(apiAuth ? { Authorization: apiAuth } : {}),
          },
          body: JSON.stringify(payload), // <-- only the 4 fields 
        });

        const text = await res.text().catch(() => null);
        let data = null;
        try { data = text ? JSON.parse(text) : null; } catch { data = null; }

        if (!res.ok) return { ok: false, status: res.status, message: (data && (data.message || data.error)) || text || `HTTP ${res.status}` };
        return { ok: true, data };
      } catch (err) {
        return { ok: false, error: err.message || String(err) };
      }
    },
    [apiUrl, apiAuth]
  );

  const publish = useCallback(async () => {
    setPublishing(true);
    try {
      const flow = getFlowSnapshot();
      const v = validateFlow(flow);
      if (!v.ok) { setToast({ type: "error", message: v.reason }); setPublishing(false); return; }

      // read latest chatbotId/userId at publish time (handles switching bots)
      const root = typeof document !== "undefined" ? document.getElementById("root") : null;
      const chatbotId = root?.dataset?.chatbotId ?? "";
      const userId = root?.dataset?.userId ?? "";

      const now = Date.now();
      const versionId = `v-${now}`;
      const iso = new Date(now).toISOString();

      const version = {
        id: versionId,
        date: iso,
        bot_id: versionId,
        chatbot_id: String(chatbotId),
        user_id: String(userId),
        payload: flow,
      };

      // local save (scoped per chatbot)
      try {
        const scopedKey = `${PUBLISH_KEY}:${String(chatbotId) || "anon"}`;
        const raw = typeof localStorage !== "undefined" ? localStorage.getItem(scopedKey) : null;
        const published = raw ? JSON.parse(raw) : { versions: [] };
        published.versions.push(version);
        if (published.versions.length > MAX_VERSIONS) published.versions = published.versions.slice(-MAX_VERSIONS);
        if (typeof localStorage !== "undefined") localStorage.setItem(scopedKey, JSON.stringify(published));
      } catch (e) { /* ignore localStorage errors */ }

      // only the 4 fields (plus json when backend still needs it)
      const nodesArray = Array.isArray(flow?.nodes) ? flow.nodes : [];

      if (apiUrl) { 
        const apiPayload = {
          bot_id: versionId,
          user_id: String(userId),
          chatbot_id: String(chatbotId),
          payload: flow,
          json: nodesArray, // kept for backend validation if needed
        };

        const res = await sendToApi(apiPayload);
        if (!res.ok) {
          const msg = res.message || res.error || "Remote publish failed";
          setToast({ type: "error", message: `Remote publish failed: ${msg}. Saved locally.` });
        } else {
          setToast({ type: "success", message: "Bot published successfully (remote)." });
        }
      } else {
        setToast({ type: "success", message: "Bot published locally." });
      }

      try { window.dispatchEvent(new CustomEvent("botPublished", { detail: { latestId: version.id } })); } catch (e) { }
      setPublishing(false);
    } catch (err) {
      console.error("Publish failed", err);
      setToast({ type: "error", message: "Publish failed.  Check console." });
      setPublishing(false);
    }
  }, [getFlowSnapshot, validateFlow, apiUrl, sendToApi]);

  const clearToast = useCallback(() => setToast(null), []);

  return { publishing, toast, publish, clearToast };
}
