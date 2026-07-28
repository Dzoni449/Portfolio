'use strict';

/* ═══════════════════════════════════════════════════════════
   Draft — chat UI
   React 18 (UMD) + Babel Standalone. localStorage persistence.
   Single-file front end, no build step, no backend.
   ═══════════════════════════════════════════════════════════ */

const { useState, useEffect, useRef, useMemo, useCallback } = React;
const LS_KEY = 'draft.conversations.v1';

/* ── User directory (senders) ────────────────────────────── */
const USERS = {
  me:    { name: 'You',   color: '#6C5AB8' },
  marta: { name: 'Marta', color: '#F19B7C' },
  aleks: { name: 'Aleks', color: '#4A9F6A' },
  nina:  { name: 'Nina',  color: '#E9B657' },
  petra: { name: 'Petra', color: '#A0A6E8' },
  jovan: { name: 'Jovan', color: '#C9412C' },
  jana:  { name: 'Jana',  color: '#7DDCB4' },
  marko: { name: 'Marko', color: '#F58270' },
};

const initial = (id) => USERS[id]?.name?.[0] || '?';

/* ── Time helpers ────────────────────────────────────────── */
const MIN = 60_000;
const HR  = 60 * MIN;
const DAY = 24 * HR;

function ago(ms) { return Date.now() - ms; }
function since(ms) {
  const d = new Date(Date.now() - ms);
  return d.getTime();
}

function formatTime(ts) {
  const d = new Date(ts);
  const h = d.getHours(),
        m = String(d.getMinutes()).padStart(2, '0'),
        ampm = h >= 12 ? 'pm' : 'am',
        h12 = ((h + 11) % 12) + 1;
  return `${h12}:${m} ${ampm}`;
}

function formatConvTime(ts) {
  const diff = Date.now() - ts;
  if (diff < HR)     return `${Math.max(1, Math.floor(diff/MIN))}m`;
  if (diff < DAY)    return `${Math.floor(diff/HR)}h`;
  if (diff < 2*DAY)  return 'yesterday';
  if (diff < 7*DAY)  return new Date(ts).toLocaleDateString('en-GB', { weekday: 'short' });
  return new Date(ts).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
}

function formatDaySep(ts) {
  const now = new Date();
  const d = new Date(ts);
  const sameDay = d.toDateString() === now.toDateString();
  if (sameDay) return 'Today';
  const yest = new Date(now); yest.setDate(now.getDate() - 1);
  if (d.toDateString() === yest.toDateString()) return 'Yesterday';
  return d.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long' });
}

function sameDay(a, b) {
  return new Date(a).toDateString() === new Date(b).toDateString();
}

/* ── Mock seed data — a busy, believable chat history ────── */
function seedConversations() {
  return [
    {
      id:      'marta',
      type:    'dm',
      name:    'Marta',
      avatar:  { text: 'M', color: '#F19B7C' },
      online:  true,
      lastSeen: since(2 * MIN),
      typing:  false,
      unread:  0,
      messages: [
        { id: 'm1', from: 'marta', text: "Hey! I heard you're finishing your portfolio. Any luck?", ts: since(DAY + 5*HR),        read: true  },
        { id: 'm2', from: 'me',    text: 'Yeah, close! Should be up this week',                    ts: since(DAY + 5*HR - 20*MIN), read: true  },
        { id: 'm3', from: 'marta', text: 'Send it when you can — my company might be hiring',       ts: since(DAY + 5*HR - 30*MIN), read: true  },
        { id: 'm4', from: 'me',    text: 'For real??',                                              ts: since(DAY + 5*HR - 32*MIN), read: true  },
        { id: 'm5', from: 'marta', text: "Yeah, junior full-stack. I'll intro you 🙌",              ts: since(DAY + 5*HR - 34*MIN), read: true  },
        { id: 'm6', from: 'me',    text: "You're the best",                                          ts: since(DAY + 5*HR - 35*MIN), read: true  },
        { id: 'm7', from: 'marta', text: 'I know',                                                   ts: since(2*HR),                read: false },
        { id: 'm8', from: 'marta', text: 'Sending your CV over tonight. Fingers crossed 🤞',        ts: since(45*MIN),              read: false },
      ],
    },
    {
      id:      'design-group',
      type:    'group',
      name:    'Design Group',
      subtitle: 'Petra, Jovan, and you',
      avatar:  { text: 'DG', color: '#A0A6E8' },
      typing:  ['jovan'],
      unread:  1,
      messages: [
        { id: 'd1', from: 'petra', text: 'Anyone free for a design review at 3?',      ts: since(3*HR),      read: true  },
        { id: 'd2', from: 'jovan', text: 'I can join!',                                 ts: since(3*HR - 4*MIN), read: true },
        { id: 'd3', from: 'petra', text: 'Great — I sent the Figma link, calendar invite going out now', ts: since(3*HR - 12*MIN), read: true },
        { id: 'd4', from: 'me',    text: "I'll be there too",                          ts: since(3*HR - 20*MIN), read: true },
        { id: 'd5', from: 'petra', text: "Perfect. Bring your questions, we'll go through the whole flow", ts: since(1*HR), read: false },
      ],
    },
    {
      id:      'aleks',
      type:    'dm',
      name:    'Aleks',
      avatar:  { text: 'A', color: '#4A9F6A' },
      online:  false,
      lastSeen: since(3*HR),
      typing:  false,
      unread:  0,
      messages: [
        { id: 'a1', from: 'aleks', text: 'How do you handle localStorage in your task app?', ts: since(5*HR), read: true },
        { id: 'a2', from: 'me',    text: 'Just useState + useEffect that syncs on change',    ts: since(5*HR - 3*MIN), read: true },
        { id: 'a3', from: 'aleks', text: 'No debouncing?',                                     ts: since(5*HR - 5*MIN), read: true },
        { id: 'a4', from: 'me',    text: "Nope — it's small enough, and I like the immediate save",  ts: since(5*HR - 6*MIN), read: true },
        { id: 'a5', from: 'aleks', text: 'Fair. Nice.',                                        ts: since(5*HR - 8*MIN), read: true },
      ],
    },
    {
      id:      'weekend',
      type:    'group',
      name:    'Weekend Plans',
      subtitle: 'Jana, Marko, and you',
      avatar:  { text: 'WP', color: '#7DDCB4' },
      typing:  false,
      unread:  0,
      messages: [
        { id: 'w1', from: 'jana',  text: 'Saturday hike? Weather looks perfect ☀️',   ts: since(2*DAY),           read: true },
        { id: 'w2', from: 'marko', text: "I'm in!",                                     ts: since(2*DAY - 20*MIN),   read: true },
        { id: 'w3', from: 'me',    text: 'Where?',                                       ts: since(2*DAY - 30*MIN),   read: true },
        { id: 'w4', from: 'jana',  text: 'Avala, meet at 8?',                            ts: since(2*DAY - 40*MIN),   read: true },
        { id: 'w5', from: 'marko', text: 'Bringing coffee ☕',                           ts: since(2*DAY - 42*MIN),   read: true },
        { id: 'w6', from: 'me',    text: "I'll bring snacks",                            ts: since(2*DAY - 45*MIN),   read: true },
        { id: 'w7', from: 'jana',  text: 'Perfect, see you at the parking lot',          ts: since(2*DAY - 50*MIN),   read: true },
      ],
    },
    {
      id:      'nina',
      type:    'dm',
      name:    'Nina',
      avatar:  { text: 'N', color: '#E9B657' },
      online:  false,
      lastSeen: since(1*DAY),
      typing:  false,
      unread:  0,
      messages: [
        { id: 'n1', from: 'nina', text: 'Coffee tomorrow?',           ts: since(3*DAY + 4*HR),        read: true },
        { id: 'n2', from: 'me',   text: "Can't, deadline stuff",       ts: since(3*DAY + 4*HR - 5*MIN), read: true },
        { id: 'n3', from: 'nina', text: 'Weekend then?',               ts: since(3*DAY + 4*HR - 8*MIN), read: true },
        { id: 'n4', from: 'me',   text: 'Yeah, Saturday works',         ts: since(3*DAY + 4*HR - 10*MIN), read: true },
        { id: 'n5', from: 'nina', text: '10am, our place',              ts: since(3*DAY + 4*HR - 12*MIN), read: true },
        { id: 'n6', from: 'me',   text: 'See you',                       ts: since(3*DAY + 4*HR - 14*MIN), read: true },
      ],
    },
  ];
}

/* ── LocalStorage ────────────────────────────────────────── */
function loadConversations() {
  try {
    const raw = localStorage.getItem(LS_KEY);
    if (raw === null) {
      const seeded = seedConversations();
      localStorage.setItem(LS_KEY, JSON.stringify(seeded));
      return seeded;
    }
    return JSON.parse(raw);
  } catch { return seedConversations(); }
}

function saveConversations(convs) {
  try { localStorage.setItem(LS_KEY, JSON.stringify(convs)); }
  catch (e) { console.warn('[draft] save failed', e); }
}

/* ═══════════════════════════════════════════════════════════
   COMPONENTS
   ═══════════════════════════════════════════════════════════ */

const Icon = ({ name, size = 16, className = '' }) => {
  const paths = {
    search:    <><circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.8-4.8"/></>,
    send:      <><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></>,
    arrow:     <><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></>,
    check:     <polyline points="20 6 9 17 4 12"/>,
    doubleCheck: <><polyline points="18 6 9 15 5 11"/><polyline points="22 6 13 15 9 11"/></>,
  };
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" className={className}>
      {paths[name]}
    </svg>
  );
};

const Avatar = ({ text, color, online, size = 40, className = '', style = {} }) => (
  <div
    className={`conv-avatar ${online ? 'online' : ''} ${className}`}
    style={{ background: color, width: size, height: size, fontSize: size * 0.4, ...style }}
    aria-hidden="true"
  >
    {text}
  </div>
);

/* ── Sidebar ─────────────────────────────────────────────── */
const ConvItem = ({ conv, active, onSelect }) => {
  const last = conv.messages[conv.messages.length - 1];
  const preview = conv.typing && conv.typing.length > 0
    ? { text: `${USERS[conv.typing[0]]?.name || 'Someone'} is typing…`, isTyping: true }
    : last
      ? { text: last.text, isYou: last.from === 'me' }
      : { text: 'No messages yet' };

  return (
    <button className={`conv-item ${active ? 'active' : ''}`} onClick={onSelect}>
      {conv.type === 'group' ? (
        <div className="conv-avatar group-avatar" aria-hidden="true">{conv.avatar.text}</div>
      ) : (
        <Avatar text={conv.avatar.text} color={conv.avatar.color} online={conv.online} />
      )}
      <div className="conv-info">
        <div className="conv-name">{conv.name}</div>
        <div className="conv-preview">
          {preview.isTyping ? (
            <span className="typing">{preview.text}</span>
          ) : (
            <>
              {preview.isYou && <span className="you">You: </span>}
              {preview.text}
            </>
          )}
        </div>
      </div>
      <div>
        <div className={`conv-time ${conv.unread ? 'unread' : ''}`}>
          {last ? formatConvTime(last.ts) : ''}
        </div>
        {conv.unread > 0 && (
          <div className="unread-badge">{conv.unread}</div>
        )}
      </div>
    </button>
  );
};

const Sidebar = ({ conversations, currentId, onSelect, mobileOpen, onClose }) => {
  const [query, setQuery] = useState('');
  const filtered = useMemo(() => {
    if (!query.trim()) return conversations;
    const q = query.toLowerCase();
    return conversations.filter(c => c.name.toLowerCase().includes(q));
  }, [conversations, query]);

  return (
    <aside className={`sidebar ${mobileOpen ? 'mobile-open' : ''}`}>
      <a href="../../index.html#work" className="back-link">
        <Icon name="arrow" size={12} />
        Back to portfolio
      </a>
      <div className="sidebar-header">
        <h1 className="sidebar-brand"><em>Draft</em></h1>
        <p className="sidebar-sub">A chat app that thinks before it sends</p>
        <div className="search-wrap">
          <div className="search-icon"><Icon name="search" size={13} /></div>
          <input
            type="text"
            className="search-input"
            placeholder="Search conversations"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
          />
        </div>
      </div>
      <div className="conv-list">
        {filtered.length === 0 ? (
          <div className="empty-search">
            <p className="empty-search-title">No matches.</p>
            <p className="empty-search-sub">Try a different name.</p>
          </div>
        ) : (
          filtered.map(conv => (
            <ConvItem
              key={conv.id}
              conv={conv}
              active={conv.id === currentId}
              onSelect={() => { onSelect(conv.id); onClose?.(); }}
            />
          ))
        )}
      </div>
    </aside>
  );
};

/* ── Chat area ───────────────────────────────────────────── */
const ChatHeader = ({ conv, onBack }) => {
  let status;
  if (conv.type === 'group') {
    status = <div className="chat-header-status">{conv.subtitle}</div>;
  } else if (conv.online) {
    status = <div className="chat-header-status online">Online</div>;
  } else {
    status = <div className="chat-header-status">Last seen {formatConvTime(conv.lastSeen)} ago</div>;
  }

  return (
    <div className="chat-header">
      <button className="chat-back-btn" onClick={onBack} aria-label="Back to conversations">
        <Icon name="arrow" size={18} />
      </button>
      {conv.type === 'group' ? (
        <div className="chat-header-avatar" style={{ background: 'linear-gradient(135deg, #A0A6E8 0%, #6C5AB8 100%)' }}>
          {conv.avatar.text}
        </div>
      ) : (
        <div className="chat-header-avatar" style={{ background: conv.avatar.color }}>
          {conv.avatar.text}
        </div>
      )}
      <div className="chat-header-info">
        <div className="chat-header-name">{conv.name}</div>
        {status}
      </div>
    </div>
  );
};

const MessageGroup = ({ messages, conv }) => {
  const first = messages[0];
  const isMine = first.from === 'me';
  const sender = USERS[first.from];
  const showAvatar = !isMine && conv.type === 'group';
  const showName = !isMine && conv.type === 'group';

  return (
    <div className={`msg-group ${isMine ? 'mine' : ''}`}>
      {!isMine && (
        <div className="msg-avatar" style={{ background: sender?.color || '#999' }} aria-hidden="true">
          {initial(first.from)}
        </div>
      )}
      <div className="msg-stack">
        {showName && (
          <div className="msg-sender-name" style={{ color: sender?.color }}>
            {sender?.name}
          </div>
        )}
        {messages.map((m, i) => (
          <div key={m.id} className="msg">{m.text}</div>
        ))}
        <div className="msg-foot">
          <span>{formatTime(first.ts)}</span>
          {isMine && (
            <span className={`msg-check ${first.read ? 'read' : ''}`}>
              <Icon name={first.read ? 'doubleCheck' : 'check'} size={11} />
            </span>
          )}
        </div>
      </div>
    </div>
  );
};

const TypingIndicator = ({ names }) => (
  <div className="typing-indicator">
    <div className="msg-avatar" style={{ background: USERS[names[0]]?.color || '#999' }} aria-hidden="true">
      {initial(names[0])}
    </div>
    <div className="typing-bubble" aria-label={`${USERS[names[0]]?.name} is typing`}>
      <span></span><span></span><span></span>
    </div>
  </div>
);

const MessageList = ({ conv }) => {
  const endRef = useRef(null);

  useEffect(() => {
    endRef.current?.scrollIntoView({ behavior: 'smooth', block: 'end' });
  }, [conv.messages.length, conv.id]);

  /* Group consecutive messages by same sender, insert day separators */
  const rendered = [];
  let lastFrom = null;
  let lastDay = null;
  let bucket = [];

  const flush = () => {
    if (bucket.length > 0) {
      rendered.push({ type: 'group', messages: bucket, from: lastFrom });
      bucket = [];
    }
  };

  conv.messages.forEach(m => {
    if (!lastDay || !sameDay(lastDay, m.ts)) {
      flush();
      rendered.push({ type: 'day', ts: m.ts });
      lastDay = m.ts;
      lastFrom = null;
    }
    if (m.from !== lastFrom) {
      flush();
      lastFrom = m.from;
    }
    bucket.push(m);
  });
  flush();

  return (
    <div className="messages">
      {rendered.map((item, i) => (
        item.type === 'day'
          ? <div key={`day-${i}`} className="day-sep">{formatDaySep(item.ts)}</div>
          : <MessageGroup key={`grp-${i}`} messages={item.messages} conv={conv} />
      ))}
      {conv.typing && conv.typing.length > 0 && (
        <TypingIndicator names={conv.typing} />
      )}
      <div ref={endRef} />
    </div>
  );
};

const Composer = ({ onSend }) => {
  const [text, setText] = useState('');
  const taRef = useRef(null);

  const submit = () => {
    const t = text.trim();
    if (!t) return;
    onSend(t);
    setText('');
    if (taRef.current) taRef.current.style.height = 'auto';
  };

  const handleKey = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      submit();
    }
  };

  const autosize = (e) => {
    setText(e.target.value);
    e.target.style.height = 'auto';
    e.target.style.height = Math.min(120, e.target.scrollHeight) + 'px';
  };

  return (
    <div className="composer">
      <div className="composer-inner">
        <textarea
          ref={taRef}
          rows={1}
          placeholder="Write a message…"
          value={text}
          onChange={autosize}
          onKeyDown={handleKey}
          maxLength={2000}
        />
        <button className="composer-send" onClick={submit} disabled={!text.trim()} aria-label="Send message">
          <Icon name="send" size={16} />
        </button>
      </div>
      <p className="composer-hint">Press <kbd>↵</kbd> to send · <kbd>⇧</kbd> <kbd>↵</kbd> for new line</p>
    </div>
  );
};

const ChatPlaceholder = () => (
  <div className="chat-placeholder">
    <div className="chat-placeholder-mark">D.</div>
    <h2 className="chat-placeholder-title">Pick a <em>conversation.</em></h2>
    <p className="chat-placeholder-sub">
      Draft is a demo chat interface — messages stay in your browser only. Choose a conversation on the left to read or reply.
    </p>
  </div>
);

/* ═══════════════════════════════════════════════════════════
   APP
   ═══════════════════════════════════════════════════════════ */
const App = () => {
  const [conversations, setConversations] = useState(loadConversations);
  const [currentId, setCurrentId] = useState(() => {
    /* On desktop, auto-select first conversation */
    return window.innerWidth > 768 ? loadConversations()[0]?.id ?? null : null;
  });
  const [mobileSidebar, setMobileSidebar] = useState(false);

  /* Persist */
  useEffect(() => { saveConversations(conversations); }, [conversations]);

  const current = conversations.find(c => c.id === currentId);

  /* On desktop, keep at least one selected */
  useEffect(() => {
    if (!currentId && conversations.length && window.innerWidth > 768) {
      setCurrentId(conversations[0].id);
    }
  }, [conversations, currentId]);

  const selectConversation = useCallback((id) => {
    setCurrentId(id);
    /* Mark as read */
    setConversations(prev => prev.map(c =>
      c.id === id
        ? { ...c, unread: 0, messages: c.messages.map(m => ({ ...m, read: true })) }
        : c
    ));
  }, []);

  const sendMessage = useCallback((text) => {
    if (!currentId) return;
    const newMsg = {
      id:   crypto.randomUUID?.() ?? String(Date.now() + Math.random()),
      from: 'me',
      text,
      ts:   Date.now(),
      read: false,
    };
    setConversations(prev => prev.map(c => {
      if (c.id !== currentId) return c;
      /* Move this conversation to top after new message */
      return { ...c, messages: [...c.messages, newMsg] };
    }));

    /* Simulate a reply after a short pause for one conversation as a demo */
    if (currentId === 'marta' && text.length > 0) {
      setTimeout(() => {
        setConversations(prev => prev.map(c => c.id === 'marta' ? { ...c, typing: ['marta'] } : c));
      }, 700);
      setTimeout(() => {
        setConversations(prev => prev.map(c => {
          if (c.id !== 'marta') return c;
          const replies = ['ok!', 'cool', "let's do it", 'noted 🙌', 'sounds good', 'send me a link', "you're on"];
          const reply = replies[Math.floor(Math.random() * replies.length)];
          return {
            ...c,
            typing: [],
            /* Mark my message as read as part of the reply */
            messages: [
              ...c.messages.map(m => m.from === 'me' ? { ...m, read: true } : m),
              {
                id:   crypto.randomUUID?.() ?? String(Date.now() + Math.random()),
                from: 'marta',
                text: reply,
                ts:   Date.now(),
                read: true,
              },
            ],
          };
        }));
      }, 2400);
    }
  }, [currentId]);

  return (
    <div className="app">
      <Sidebar
        conversations={conversations}
        currentId={currentId}
        onSelect={selectConversation}
        mobileOpen={mobileSidebar}
        onClose={() => setMobileSidebar(false)}
      />
      {mobileSidebar && (
        <div className="mobile-overlay show" onClick={() => setMobileSidebar(false)} />
      )}
      <section className="chat">
        {current ? (
          <>
            <ChatHeader conv={current} onBack={() => setMobileSidebar(true)} />
            <MessageList conv={current} />
            <Composer onSend={sendMessage} />
          </>
        ) : (
          <ChatPlaceholder />
        )}
      </section>
    </div>
  );
};

ReactDOM.createRoot(document.getElementById('root')).render(<App />);
