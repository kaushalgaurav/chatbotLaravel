// src/hooks/usePublish.js
import { useCallback, useState, useRef } from "react";

const PUBLISH_KEY = "published-flow:v1";
const MAX_VERSIONS = 10;

export default function usePublish(getFlowSnapshot, options = {}) {
  const { apiUrl = null, apiAuth = null } = options;
  const [publishing, setPublishing] = useState(false);
  const [toast, setToast] = useState(null);
  const publishingRef = useRef(false);

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
          body: JSON.stringify(payload),
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

  /**
   * publish(options)
   * options = {
   *   is_published: boolean (default false),
   *   skipValidation: boolean (default false) - useful for autosave/drafts
   * }
   */
const publish = useCallback(async (opts = {}) => {
  const { is_published = false, skipValidation = false, silent = false } = opts;

  // Prevent overlapping publishes
  if (publishingRef.current) {
    // already publishing; skip this invocation
    return { ok: false, skipped: true, reason: "Already publishing" };
  }

  setPublishing(true);
  publishingRef.current = true;

  try {
    const flow = getFlowSnapshot();

    if (!skipValidation) {
      const v = validateFlow(flow);
      if (!v.ok) {
        if (!silent) setToast({ type: "error", message: v.reason });
        setPublishing(false);
        publishingRef.current = false;
        return { ok: false, reason: v.reason };
      }
    }

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
      is_published: !!is_published,
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

    // prepare API payload (only necessary fields)
    const nodesArray = Array.isArray(flow?.nodes) ? flow.nodes : [];

    if (apiUrl) {
      const apiPayload = {
        bot_id: versionId,
        user_id: String(userId),
        chatbot_id: String(chatbotId),
        is_published: Number(!!is_published),
        payload: flow,
        json: nodesArray, 
      };

      const res = await sendToApi(apiPayload);
      if (!res.ok) {
        const msg = res.message || res.error || "Remote publish failed";
        if (!silent) setToast({ type: "error", message: `Remote publish failed: ${msg}. Saved locally.` });
        setPublishing(false);
        publishingRef.current = false;
        return { ok: false, reason: msg };
      } else {
        if (!silent) {
          setToast({ type: "success", message: is_published ? "Bot published successfully (remote)." : "Draft saved remotely." });
        } 
      }
    } else {
      if (!silent) {
        setToast({ type: "success", message: is_published ? "Bot published locally." : "Draft saved locally." });
      }
    }

    try { window.dispatchEvent(new CustomEvent("botPublished", { detail: { latestId: version.id, is_published: !!is_published } })); } catch (e) { }

    setPublishing(false);
    publishingRef.current = false;
    return { ok: true, versionId };
  } catch (err) {
    console.error("Publish failed", err);
    if (!silent) setToast({ type: "error", message: "Publish failed.  Check console." });
    setPublishing(false);
    publishingRef.current = false;
    return { ok: false, error: err.message || String(err) };
  }
}, [getFlowSnapshot, validateFlow, apiUrl, sendToApi]);


  const clearToast = useCallback(() => setToast(null), []);

  return { publishing, toast, publish, clearToast };
}
