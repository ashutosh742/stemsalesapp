// AgentChatScreen — chat-style transcript with the selected agent.
// Renders text, tool-call cards (endpoint badge shows real PHP method),
// insight cards, pattern cards, and an actions row with Approve/Later buttons.

import React, { useEffect, useRef, useState } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, Platform,
  ActivityIndicator, TextInput, KeyboardAvoidingView,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { agents, TOOL_ENDPOINTS, ANAYA_SCRIPT, WARROOM_SCRIPT } from '../data/agents';

const ICON_MAP = {
  sparkles: 'sparkles',
  mic: 'mic',
  compass: 'compass',
  flame: 'flame',
  wand: 'color-wand',
  trophy: 'trophy',
};

const SCRIPTS = {
  anaya: ANAYA_SCRIPT,
  warroom: WARROOM_SCRIPT,
};

// Fallback script for agents without a hand-written conversation
function genericScript(agent) {
  return [
    { role: 'assistant', kind: 'text', text: `Hi, I'm ${agent.name}. ${agent.desc}` },
    { role: 'assistant', kind: 'text', text: 'Try one of the tool shortcuts below, or ask me a question.' },
  ];
}

export default function AgentChatScreen({ route, navigation }) {
  const agentId = route?.params?.agentId || 'anaya';
  const agent = agents.find((a) => a.id === agentId) || agents[0];
  const script = SCRIPTS[agentId] || genericScript(agent);

  const [messages, setMessages] = useState([]);
  const [streaming, setStreaming] = useState(true);
  const [showTools, setShowTools] = useState(false);
  const [input, setInput] = useState('');
  const scrollRef = useRef(null);

  // Stream messages in one at a time for a "thinking" effect
  useEffect(() => {
    let cancelled = false;
    let i = 0;
    function tick() {
      if (cancelled || i >= script.length) {
        if (!cancelled) setStreaming(false);
        return;
      }
      const next = script[i];
      i += 1;
      setMessages((prev) => [...prev, next]);
      setTimeout(tick, next.role === 'tool' ? 700 : 450);
    }
    tick();
    return () => { cancelled = true; };
  }, [agentId]);

  useEffect(() => {
    setTimeout(() => scrollRef.current?.scrollToEnd({ animated: true }), 50);
  }, [messages]);

  return (
    <KeyboardAvoidingView
      style={{ flex: 1, backgroundColor: colors.cardAlt }}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <StatusBar barStyle="light-content" />

      {/* Agent header */}
      <LinearGradient
        colors={agent.gradient}
        start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
        style={styles.header}
      >
        <Pressable onPress={() => navigation.goBack()} hitSlop={12}>
          <Ionicons name="chevron-back" size={26} color="#fff" />
        </Pressable>
        <View style={styles.headerIcon}>
          <Ionicons name={ICON_MAP[agent.icon] || 'sparkles'} size={20} color="#fff" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>{agent.name}</Text>
          <Text style={styles.headerSub}>{agent.role} · {agent.permission}</Text>
        </View>
        <Pressable onPress={() => setShowTools((s) => !s)} hitSlop={10} style={styles.toolsBtn}>
          <Ionicons name="construct" size={16} color="#fff" />
          <Text style={styles.toolsBtnText}>Tools</Text>
        </Pressable>
      </LinearGradient>

      {/* Tools panel */}
      {showTools && (
        <View style={styles.toolsPanel}>
          <Text style={styles.toolsPanelTitle}>Allowed tools · cap {agent.cap}</Text>
          {agent.tools.map((t) => (
            <View key={t} style={styles.toolRow}>
              <Text style={styles.toolName}>{t}</Text>
              <Text style={styles.toolEndpoint} numberOfLines={1}>
                {TOOL_ENDPOINTS[t] || (t === 'ALL READ TOOLS' ? 'all AIAgents/* read models' : '—')}
              </Text>
            </View>
          ))}
        </View>
      )}

      {/* Transcript */}
      <ScrollView
        ref={scrollRef}
        contentContainerStyle={{ padding: 14, paddingBottom: 24 }}
        showsVerticalScrollIndicator={false}
      >
        {messages.map((m, idx) => (
          <Message key={idx} msg={m} agent={agent} />
        ))}
        {streaming && (
          <View style={styles.typingRow}>
            <ActivityIndicator size="small" color={agent.gradient[1]} />
            <Text style={styles.typingText}>{agent.name} is thinking…</Text>
          </View>
        )}
      </ScrollView>

      {/* Composer */}
      <View style={styles.composer}>
        <TextInput
          style={styles.composerInput}
          placeholder={`Ask ${agent.name}…`}
          placeholderTextColor={colors.textMuted}
          value={input}
          onChangeText={setInput}
        />
        <Pressable
          style={[styles.sendBtn, !input && { opacity: 0.4 }]}
          disabled={!input}
          onPress={() => {
            setMessages((prev) => [...prev, { role: 'user', kind: 'text', text: input }]);
            setInput('');
          }}
        >
          <Ionicons name="arrow-up" size={18} color="#fff" />
        </Pressable>
      </View>
    </KeyboardAvoidingView>
  );
}

function Message({ msg, agent }) {
  if (msg.role === 'user') {
    return (
      <View style={[styles.bubble, styles.userBubble]}>
        <Text style={styles.userText}>{msg.text}</Text>
      </View>
    );
  }
  if (msg.role === 'tool') {
    return <ToolCallCard msg={msg} agent={agent} />;
  }
  // assistant
  if (msg.kind === 'insights') return <InsightList items={msg.items} agent={agent} />;
  if (msg.kind === 'pattern') return <PatternCard text={msg.text} agent={agent} />;
  if (msg.kind === 'actions') return <ActionsCard msg={msg} agent={agent} />;
  // default text
  return (
    <View style={[styles.bubble, styles.assistantBubble]}>
      <Text style={styles.assistantText}>{msg.text}</Text>
    </View>
  );
}

function ToolCallCard({ msg }) {
  const endpoint = TOOL_ENDPOINTS[msg.name] || '—';
  return (
    <View style={styles.toolCard}>
      <View style={styles.toolCardHead}>
        <Ionicons name="flash" size={13} color={colors.btnFrom} />
        <Text style={styles.toolCardName}>{msg.name}</Text>
        <View style={styles.toolEndpointBadge}>
          <Text style={styles.toolEndpointBadgeText} numberOfLines={1}>{endpoint}</Text>
        </View>
        <Text style={styles.toolLatency}>{msg.latency}ms</Text>
      </View>
      <Text style={styles.toolArgs} numberOfLines={2}>
        args: {JSON.stringify(msg.args)}
      </Text>
      <View style={styles.toolResultBox}>
        <Text style={styles.toolResultText} numberOfLines={4}>
          {JSON.stringify(msg.result, null, 2)}
        </Text>
      </View>
    </View>
  );
}

function InsightList({ items, agent }) {
  return (
    <View style={{ gap: 8, marginVertical: 6 }}>
      {items.map((it, i) => (
        <View key={i} style={styles.insightCard}>
          <View style={styles.insightHead}>
            <Text style={styles.insightTitle}>{it.title}</Text>
            <View style={[styles.insightTag, { backgroundColor: agent.gradient[1] + '20' }]}>
              <Text style={[styles.insightTagText, { color: agent.gradient[1] }]}>{it.tag}</Text>
            </View>
          </View>
          <Text style={styles.insightBody}>{it.body}</Text>
        </View>
      ))}
    </View>
  );
}

function PatternCard({ text, agent }) {
  return (
    <View style={[styles.patternCard, { borderLeftColor: agent.gradient[1] }]}>
      <View style={styles.patternHead}>
        <Ionicons name="trending-up" size={14} color={agent.gradient[1]} />
        <Text style={[styles.patternKicker, { color: agent.gradient[1] }]}>Pattern detected</Text>
      </View>
      <Text style={styles.patternText}>{text}</Text>
    </View>
  );
}

function ActionsCard({ msg, agent }) {
  const [chosen, setChosen] = useState(null);
  return (
    <View style={styles.actionsCard}>
      <Text style={styles.actionsText}>{msg.text}</Text>
      <View style={styles.actionsRow}>
        {msg.actions.map((a) => {
          const isApprove = a.id === 'approve';
          const isChosen = chosen === a.id;
          return (
            <Pressable
              key={a.id}
              onPress={() => setChosen(a.id)}
              style={[
                styles.actionBtn,
                isApprove ? { backgroundColor: agent.gradient[1] } : styles.actionBtnGhost,
                isChosen && { opacity: 0.7 },
              ]}
            >
              {isApprove && <Ionicons name="checkmark" size={14} color="#fff" />}
              <Text style={[
                styles.actionBtnText,
                isApprove ? { color: '#fff' } : { color: colors.text },
              ]}>{isChosen && isApprove ? 'Sent' : a.label}</Text>
            </Pressable>
          );
        })}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  header: {
    flexDirection: 'row', alignItems: 'center', gap: 10,
    paddingTop: Platform.OS === 'ios' ? 54 : 36,
    paddingBottom: 14, paddingHorizontal: 14,
  },
  headerIcon: {
    width: 36, height: 36, borderRadius: 11,
    backgroundColor: 'rgba(255,255,255,0.22)',
    alignItems: 'center', justifyContent: 'center',
  },
  headerTitle: { color: '#fff', fontSize: 17, fontWeight: '700' },
  headerSub: { color: 'rgba(255,255,255,0.8)', fontSize: 11, marginTop: 1 },
  toolsBtn: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    backgroundColor: 'rgba(255,255,255,0.2)',
    paddingHorizontal: 10, paddingVertical: 6, borderRadius: 8,
  },
  toolsBtnText: { color: '#fff', fontWeight: '600', fontSize: 12 },

  toolsPanel: {
    backgroundColor: colors.card,
    borderBottomWidth: 1, borderColor: colors.border,
    paddingVertical: 10, paddingHorizontal: 16,
  },
  toolsPanelTitle: { color: colors.textMuted, fontSize: 11, fontWeight: '600', textTransform: 'uppercase', letterSpacing: 0.4, marginBottom: 6 },
  toolRow: { flexDirection: 'row', justifyContent: 'space-between', gap: 8, paddingVertical: 3 },
  toolName: { color: colors.text, fontSize: 12.5, fontWeight: '500' },
  toolEndpoint: { color: colors.textMuted, fontSize: 11, fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace', flexShrink: 1 },

  bubble: { padding: 12, borderRadius: 14, marginVertical: 4, maxWidth: '88%' },
  assistantBubble: {
    backgroundColor: colors.card, alignSelf: 'flex-start',
    borderWidth: 1, borderColor: colors.border, borderBottomLeftRadius: 4,
  },
  assistantText: { color: colors.text, fontSize: 13.5, lineHeight: 19 },
  userBubble: {
    backgroundColor: colors.btnFrom, alignSelf: 'flex-end', borderBottomRightRadius: 4,
  },
  userText: { color: '#fff', fontSize: 13.5, lineHeight: 19 },

  toolCard: {
    backgroundColor: '#0F172A', borderRadius: 12, padding: 12, marginVertical: 6,
  },
  toolCardHead: { flexDirection: 'row', alignItems: 'center', gap: 6, flexWrap: 'wrap' },
  toolCardName: { color: '#FCD34D', fontWeight: '700', fontSize: 12.5, fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace' },
  toolEndpointBadge: { backgroundColor: 'rgba(252,211,77,0.15)', paddingHorizontal: 7, paddingVertical: 2, borderRadius: 5, flexShrink: 1 },
  toolEndpointBadgeText: { color: '#FCD34D', fontSize: 10.5, fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace' },
  toolLatency: { color: '#64748B', fontSize: 10.5, marginLeft: 'auto' },
  toolArgs: { color: '#94A3B8', fontSize: 11, marginTop: 8, fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace' },
  toolResultBox: { marginTop: 8, backgroundColor: '#1E293B', borderRadius: 8, padding: 10 },
  toolResultText: { color: '#CBD5E1', fontSize: 11, fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace', lineHeight: 16 },

  insightCard: {
    backgroundColor: colors.card,
    borderRadius: 12, padding: 12,
    borderWidth: 1, borderColor: colors.border,
  },
  insightHead: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 4 },
  insightTitle: { color: colors.text, fontWeight: '700', fontSize: 13, flex: 1, paddingRight: 8 },
  insightTag: { paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6 },
  insightTagText: { fontSize: 10, fontWeight: '600' },
  insightBody: { color: colors.textMuted, fontSize: 12.5, lineHeight: 18 },

  patternCard: {
    backgroundColor: colors.cardAlt,
    borderRadius: 10, padding: 12, marginVertical: 6,
    borderLeftWidth: 3,
  },
  patternHead: { flexDirection: 'row', alignItems: 'center', gap: 5, marginBottom: 4 },
  patternKicker: { fontSize: 10.5, fontWeight: '700', textTransform: 'uppercase', letterSpacing: 0.5 },
  patternText: { color: colors.text, fontSize: 13, lineHeight: 18.5 },

  actionsCard: {
    backgroundColor: colors.card, borderRadius: 14, padding: 14, marginVertical: 6,
    borderWidth: 1, borderColor: colors.border,
  },
  actionsText: { color: colors.text, fontSize: 13.5, lineHeight: 19, marginBottom: 10 },
  actionsRow: { flexDirection: 'row', gap: 8 },
  actionBtn: {
    flexDirection: 'row', alignItems: 'center', gap: 5,
    paddingVertical: 9, paddingHorizontal: 14,
    borderRadius: 9,
  },
  actionBtnGhost: { backgroundColor: colors.cardAlt, borderWidth: 1, borderColor: colors.border },
  actionBtnText: { fontSize: 12.5, fontWeight: '600' },

  typingRow: { flexDirection: 'row', alignItems: 'center', gap: 8, paddingVertical: 8, paddingLeft: 4 },
  typingText: { color: colors.textMuted, fontSize: 12 },

  composer: {
    flexDirection: 'row', gap: 8, alignItems: 'center',
    padding: 10, paddingBottom: Platform.OS === 'ios' ? 24 : 12,
    backgroundColor: colors.card,
    borderTopWidth: 1, borderColor: colors.border,
  },
  composerInput: {
    flex: 1, backgroundColor: colors.cardAlt, borderRadius: 10,
    paddingHorizontal: 14, paddingVertical: 10, fontSize: 13.5, color: colors.text,
  },
  sendBtn: {
    width: 38, height: 38, borderRadius: 10,
    backgroundColor: colors.btnFrom,
    alignItems: 'center', justifyContent: 'center',
  },
});
