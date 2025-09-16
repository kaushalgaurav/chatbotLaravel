// src/pages/SharePage.jsx
import React, { useCallback, useMemo, useState, useEffect } from "react";
import { Topbar } from "../components"; // expects Topbar exported from your components barrel
import { useReactFlow } from "@xyflow/react";

// Local storage key used by the dummy publish flow
const PUBLISH_KEY = "published-flow:v1";

// Small list of embed types
const EMBED_TYPES = [
  { id: "livechat", label: "Livechat" },
  { id: "fullpage", label: "Fullpage" },
  { id: "embed", label: "Embed" },
  { id: "popup", label: "Popup" },
];

/** Safe read latest published snapshot from localStorage */
function readLatestPublished() {
  try {
    const raw = localStorage.getItem(PUBLISH_KEY);
    if (!raw) return null;
    const parsed = JSON.parse(raw);
    const last = parsed?.versions?.length
      ? parsed.versions[parsed.versions.length - 1]
      : null;
    return last ? { id: last.id, ts: last.ts, flow: last.flow } : null;
  } catch {
    return null;
  }
}

/* ---------- Small presentational subcomponents (kept in-file for simplicity) ---------- */

function LeftNav({ onExport, published }) {
  return (
    <aside
      style={{ width: "14rem" }}
      className="flex-shrink-0 bg-white border rounded p-4 small"
    >
      <h4 className="fw-semibold mb-3">Embed into your website</h4>

      <ul className="list-unstyled mb-0">
        <li className="py-2 px-2 rounded hover-bg">
          Embed into your website
        </li>
        <li className="py-2 px-2 rounded hover-bg">Share with a link</li>
        <li className="py-2 px-2 rounded hover-bg">Share as a template</li>
        <li role="button" className="py-2 px-2 rounded hover-bg">
          Export Flow
        </li>
        <li
          role="button"
          onClick={onExport}
          className="py-2 px-2 rounded hover-bg cursor-pointer"
        >
          Export JSON
        </li>
      </ul>

      <div className="mt-4 small text-muted">
        <div className="fw-medium">Preview</div>
        <div className="mt-1">
          {published ? "Published version available" : "No published version"}
        </div>
      </div>
    </aside>
  );
}

function EmbedTiles({ selected, onSelect }) {
  return (
    <div className="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
      {EMBED_TYPES.map((t) => {
        const isSelected = selected === t.id;
        return (
          <div key={t.id} className="col">
            <button
              type="button"
              onClick={() => onSelect(t.id)}
              className={`d-flex align-items-start w-100 p-3 rounded border text-start small ${
                isSelected
                  ? "border-danger bg-light"
                  : "border-secondary bg-white"
              }`}
              aria-pressed={isSelected}
              style={{ textAlign: "left" }}
            >
              <div
                className="d-flex align-items-center justify-content-center rounded bg-white border me-3"
                style={{ width: 40, height: 40 }}
              >
                <div style={{ width: 20, height: 20, borderRadius: 6, background: "#ffb6c1" }} />
              </div>
              <div>
                <div className="fw-semibold">{t.label}</div>
                <div className="small text-muted">Preview</div>
              </div>
            </button>
          </div>
        );
      })}
    </div>
  );
}

function CodeBox({ code }) {
  const [copied, setCopied] = useState(false);

  const handleCopy = useCallback(async () => {
    try {
      await navigator.clipboard.writeText(code);
      setCopied(true);
    } catch {
      // fallback
      const ta = document.createElement("textarea");
      ta.value = code;
      ta.style.position = "fixed";
      ta.style.left = "-9999px";
      document.body.appendChild(ta);
      ta.select();
      try {
        document.execCommand("copy");
        setCopied(true);
      } catch {}
      document.body.removeChild(ta);
    }
    setTimeout(() => setCopied(false), 1400);
  }, [code]);

  return (
    <div className="position-relative">
      <textarea
        readOnly
        value={code}
        className="form-control font-monospace small"
        style={{ height: 160, background: "#f8fafc", resize: "none" }}
        aria-label="Embed code"
      />
      <div className="position-absolute" style={{ right: 12, top: 12 }}>
        <button
          onClick={handleCopy}
          className={`btn btn-sm ${copied ? "btn-success" : "btn-danger"}`}
          type="button"
        >
          {copied ? "Copied ✓" : "COPY"}
        </button>
      </div>
    </div>
  );
}

/* ---------- Main SharePage ---------- */

export default function SharePage() {
  // published snapshot (read into state & update when a publish event occurs)
  const [published, setPublished] = useState(() => readLatestPublished());

  useEffect(() => {
    function onPublished() {
      setPublished(readLatestPublished());
    }
    window.addEventListener("botPublished", onPublished);
    return () => window.removeEventListener("botPublished", onPublished);
  }, []);

  const [selectedEmbed, setSelectedEmbed] = useState("popup");
  const [proactive, setProactive] = useState(false);

  // derived values (memoized)
  const shareUrl = useMemo(() => {
    const id = published?.id || "untitled";
    return `${window.location.origin}/bot/preview/${id}`;
  }, [published]);

  const embedCode = useMemo(() => {
    const botId = published?.id || "UNPUBLISHED";
    const origin = window.location.origin;
    return `<!-- Bot embed (${selectedEmbed}) -->
<script>
(function(){
  var BOT="${botId}",TYPE="${selectedEmbed}",PRO=${
      proactive ? 1 : 0
    },BASE="${origin}";
  var f=document.createElement('iframe');
  f.src=BASE+'/bot/preview/'+BOT+'?embed='+TYPE+'&proactive='+(PRO?1:0);
  Object.assign(f.style,{position:'fixed',right:'20px',bottom:'80px',width:'360px',height:'500px',border:0,borderRadius:'12px',boxShadow:'0 8px 30px rgba(0,0,0,0.18)',zIndex:999998});
  f.title='Chatbot'; f.loading='lazy';
  var b=document.createElement('button'); b.innerText='💬';
  Object.assign(b.style,{position:'fixed',right:'20px',bottom:'20px',zIndex:999999,padding:'10px',borderRadius:'999px',cursor:'pointer'});
  var shown=false;
  b.onclick=function(){ if(shown){ f.remove(); shown=false } else { document.body.appendChild(f); shown=true } };
  document.body.appendChild(b);
  window.__bot_widget=window.__bot_widget||{};
  window.__bot_widget.sendPrefill=function(d){ try{ f.contentWindow.postMessage({type:'prefill',data:d}, BASE); }catch(e){} };
})();
</script>`;
  }, [selectedEmbed, proactive, published]);

  const { toObject } = useReactFlow();

  const handleExport = useCallback(() => {
    console.log("[export] start");

    let flow = null;

    // 1) try toObject()
    try {
      const maybe = toObject();
      console.log("[export] toObject() =>", maybe);
      // treat as valid only if nodes/edges arrays have items
      if (
        maybe &&
        ((Array.isArray(maybe.nodes) && maybe.nodes.length) ||
          (Array.isArray(maybe.edges) && maybe.edges.length))
      ) {
        flow = maybe;
        console.log("[export] using toObject()");
      } else {
        console.log("[export] toObject() empty");
      }
    } catch (e) {
      console.warn("[export] toObject() error", e);
    }

    // 2) fallback: localStorage 'current-flow' (saved by FlowApp)
    if (!flow) {
      try {
        const raw = localStorage.getItem("current-flow");
        console.log(
          "[export] localStorage[current-flow] raw =>",
          raw ? raw.slice(0, 200) : raw
        );
        if (raw) {
          const parsed = JSON.parse(raw);
          if (
            parsed &&
            ((Array.isArray(parsed.nodes) && parsed.nodes.length) ||
              (Array.isArray(parsed.edges) && parsed.edges.length))
          ) {
            flow = parsed;
            console.log("[export] using localStorage current-flow");
          } else {
            console.log("[export] current-flow present but empty");
          }
        } else {
          console.log("[export] no current-flow in localStorage");
        }
      } catch (e) {
        console.warn("[export] localStorage parse error", e);
      }
    }

    // 3) fallback: published snapshot (existing)
    if (!flow) {
      console.log("[export] falling back to published?.flow");
      flow = published?.flow || { nodes: [], edges: [] };
    }

    console.log(
      "[export] final flow -> nodes:",
      flow?.nodes?.length,
      "edges:",
      flow?.edges?.length
    );

    // create and download blob
    // sanitize and download
    function sanitizeFlowForExport(f) {
      const nodes = (f.nodes || []).map((n) => ({
        id: n.id,
        type: n.type,
        position: n.position,
        data: n.data,
      }));

      const edges = (f.edges || []).map((e) => ({
        id: e.id,
        source: e.source,
        target: e.target,
        ...(e.animated ? { animated: true } : {}),
      }));

      const sanitized = { nodes, edges };
      if (f.viewport) sanitized.viewport = f.viewport;
      return sanitized;
    }

    try {
      const cleaned = sanitizeFlowForExport(flow);
      const blob = new Blob([JSON.stringify(cleaned, null, 2)], {
        type: "application/json",
      });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = `flow-${published?.id || "live"}.json`;
      a.click();
      URL.revokeObjectURL(url);
      console.log("[export] download triggered (sanitized)");
    } catch (e) {
      console.error("[export] download failed", e);
      alert("Export failed — check console.");
    }
  }, [toObject, published]);

  const handleCopyShare = useCallback(() => {
    try {
      navigator.clipboard.writeText(shareUrl);
      alert("Share link copied");
    } catch {
      alert("Copy failed — select and copy the URL manually.");
    }
  }, [shareUrl]);

  return (
    <div className="min-vh-100" style={{ background: "#f8fafc" }}>
      {/* Use app's Topbar */}
      <Topbar />

      <div className="container-xl">
        <div className="row gx-4 gy-4 py-4">
          <div className="col-12">
            <div className="d-flex gap-4 align-items-start">
              <LeftNav onExport={handleExport} published={!!published} />

              <main className="flex-grow-1">
                <div className="bg-white border rounded p-4 shadow-sm">
                  <h2 className="h6 fw-semibold mb-3">EMBED INTO YOUR WEBSITE</h2>

                  <section className="mb-4">
                    <div className="small text-muted mb-2">
                      1. Choose how you want your bot to display:
                    </div>
                    <EmbedTiles
                      selected={selectedEmbed}
                      onSelect={setSelectedEmbed}
                    />
                  </section>

                  <section className="mb-4 d-flex align-items-start justify-content-between">
                    <div>
                      <div className="small text-muted mb-1">
                        2. Show a proactive message?
                      </div>
                      <label className="form-check form-check-inline align-items-center small">
                        <input
                          className="form-check-input"
                          type="checkbox"
                          checked={proactive}
                          onChange={() => setProactive((s) => !s)}
                        />
                        <span className="form-check-label ms-2">Show proactive message</span>
                      </label>
                    </div>

                    <div className="text-end flex-shrink-0">
                      <div className="small text-muted">Share URL</div>
                      <input
                        readOnly
                        value={shareUrl}
                        className="form-control form-control-sm mt-1"
                        style={{ width: 320 }}
                      />
                    </div>
                  </section>

                  <section>
                    <div className="small text-muted mb-2">
                      3. Copy and paste this code anywhere in the &lt;body&gt;:
                    </div>
                    <CodeBox code={embedCode} />
                    <div className="mt-3 small text-muted">
                      Improve page load by lazy-loading the embed. Add this code where
                      you'd like the bot.
                    </div>
                  </section>
                </div>

                <div className="mt-3 d-flex justify-content-end gap-2">
                  <button onClick={handleExport} className="btn btn-outline-secondary btn-sm">
                    Export JSON
                  </button>
                  <button onClick={handleCopyShare} className="btn btn-outline-secondary btn-sm">
                    Copy share link
                  </button>
                  <button onClick={() => alert("Save as template (demo)")} className="btn btn-outline-secondary btn-sm">
                    Save as template
                  </button>
                  <button onClick={() => alert("Publish (demo)")} className="btn btn-danger btn-sm">
                    Publish
                  </button>
                </div>
              </main>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
