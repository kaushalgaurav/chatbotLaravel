// src/hooks/usePublish.js
import { useCallback, useState } from "react";

const PUBLISH_KEY = "published-flow:v1";
const MAX_VERSIONS = 10;

/**
 * usePublish
 * - accepts a function `getFlowSnapshot` that returns the current flow object (e.g. react-flow toObject)
 * - returns: { publishing, toast, publish }
 *
 * toast: { type: 'success'|'error'|'info', message }
 */
export default function usePublish(getFlowSnapshot) {
  const [publishing, setPublishing] = useState(false);
  const [toast, setToast] = useState(null);

  const validateFlow = useCallback((flowObj) => {
    if (!flowObj) return { ok: false, reason: "Empty flow" };
    const nodeCount = (flowObj.nodes && flowObj.nodes.length) || 0;
    if (nodeCount === 0) return { ok: false, reason: "Add at least one node before publishing." };
    // Add other domain validations here as needed
    return { ok: true };
  }, []);

  const publish = useCallback(async () => {
    try {
      setPublishing(true);
      const flowSnapshot = getFlowSnapshot();
      const validated = validateFlow(flowSnapshot);
      if (!validated.ok) {
        setToast({ type: "error", message: validated.reason });
        setPublishing(false);
        return;
      }

      const now = Date.now();
      const publishedRaw = localStorage.getItem(PUBLISH_KEY);
      let published = publishedRaw ? JSON.parse(publishedRaw) : { versions: [] };

      const version = {
        id: `v-${now}`,
        ts: now,
        summary: {
          nodeCount: (flowSnapshot.nodes || []).length,
          edgeCount: (flowSnapshot.edges || []).length,
        },
        flow: flowSnapshot,
      };

      published.versions.push(version);
      if (published.versions.length > MAX_VERSIONS) {
        published.versions = published.versions.slice(-MAX_VERSIONS);
      }

      localStorage.setItem(PUBLISH_KEY, JSON.stringify(published));

      // notify other windows/components that a publish happened
      try {
        window.dispatchEvent(new CustomEvent("botPublished", { detail: { latestId: version.id } }));
      } catch (e) {
        // ignore if dispatch fails for any reason
      }

      setToast({ type: "success", message: "Bot published successfully." });
      setPublishing(false);
    } catch (err) {
      console.error("Publish failed", err);
      setToast({ type: "error", message: "Publish failed. Check console." });
      setPublishing(false);
    }
  }, [getFlowSnapshot, validateFlow]);

  const clearToast = useCallback(() => setToast(null), []);

  return {
    publishing,
    toast,
    publish,
    clearToast,
  };
}
