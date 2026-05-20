import React, { useState, useMemo } from 'react';
import { View, Text, TextInput, FlatList, TouchableOpacity, StyleSheet, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { leads, stageColors } from '../data/mock';

const STAGES = ['All', 'New', 'Site Visit', 'Proposal Sent', 'Negotiation', 'Won'];

export default function LeadsScreen({ navigation }) {
  const [q, setQ] = useState('');
  const [stage, setStage] = useState('All');

  const filtered = useMemo(() => leads.filter(l => {
    const matchQ = !q || l.school.toLowerCase().includes(q.toLowerCase()) || l.city.toLowerCase().includes(q.toLowerCase());
    const matchS = stage === 'All' || l.stage === stage;
    return matchQ && matchS;
  }), [q, stage]);

  return (
    <View style={{ flex: 1, backgroundColor: colors.cardAlt }}>
      <View style={styles.header}>
        <Text style={styles.title}>Leads</Text>
        <TouchableOpacity style={styles.addBtn} onPress={() => navigation?.navigate?.('NewLead')}>
          <Ionicons name="add" size={20} color="#fff" />
        </TouchableOpacity>
      </View>

      <View style={styles.searchWrap}>
        <Ionicons name="search" size={16} color={colors.textMuted} />
        <TextInput
          value={q} onChangeText={setQ}
          placeholder="Search schools, cities…"
          placeholderTextColor={colors.textMuted}
          style={styles.search}
        />
      </View>

      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.chipRow}>
        {STAGES.map(s => (
          <TouchableOpacity key={s} onPress={() => setStage(s)} style={[styles.chip, stage === s && styles.chipActive]}>
            <Text style={[styles.chipText, stage === s && styles.chipTextActive]}>{s}</Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      <FlatList
        data={filtered}
        keyExtractor={i => i.id}
        contentContainerStyle={{ padding: 12 }}
        renderItem={({ item }) => (
          <TouchableOpacity activeOpacity={0.8} style={styles.card}>
            <View style={styles.row}>
              <Text style={styles.id}>{item.id}</Text>
              <Text style={[styles.stageBadge, { backgroundColor: (stageColors[item.stage] || '#888') + '22', color: stageColors[item.stage] || '#888' }]}>
                {item.stage}
              </Text>
            </View>
            <Text style={styles.school}>{item.school}</Text>
            <View style={styles.metaRow}>
              <Ionicons name="location-outline" size={12} color={colors.textMuted} />
              <Text style={styles.meta}>{item.city}</Text>
              <Text style={styles.dot}>·</Text>
              <Ionicons name="cube-outline" size={12} color={colors.textMuted} />
              <Text style={styles.meta}>{item.program}</Text>
            </View>
            <View style={styles.footerRow}>
              <Text style={styles.value}>{item.value}</Text>
              <Text style={styles.owner}>{item.owner} · {item.updated}</Text>
            </View>
          </TouchableOpacity>
        )}
        ListEmptyComponent={
          <View style={{ alignItems: 'center', marginTop: 60 }}>
            <Ionicons name="file-tray-outline" size={42} color="#CBD5E1" />
            <Text style={{ color: colors.textMuted, marginTop: 8 }}>No leads match your filters</Text>
          </View>
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: 16, paddingTop: 50, backgroundColor: '#fff' },
  title: { fontSize: 24, fontWeight: '800', color: colors.text },
  addBtn: { backgroundColor: colors.brandPink, width: 38, height: 38, borderRadius: 19, justifyContent: 'center', alignItems: 'center' },
  searchWrap: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', marginHorizontal: 16, paddingHorizontal: 12, borderRadius: 12, borderWidth: 1, borderColor: colors.border, gap: 8 },
  search: { flex: 1, paddingVertical: 10, color: colors.text },
  chipRow: { paddingHorizontal: 16, paddingVertical: 12, gap: 8 },
  chip: { paddingHorizontal: 14, paddingVertical: 7, borderRadius: 16, backgroundColor: '#fff', borderWidth: 1, borderColor: colors.border, marginRight: 8 },
  chipActive: { backgroundColor: colors.btnFrom, borderColor: colors.btnFrom },
  chipText: { color: colors.text, fontSize: 12, fontWeight: '600' },
  chipTextActive: { color: '#fff' },
  card: { backgroundColor: '#fff', padding: 14, borderRadius: 14, marginBottom: 10, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 4, elevation: 1 },
  row: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  id: { fontSize: 11, color: colors.textMuted, fontWeight: '700', letterSpacing: 0.5 },
  stageBadge: { fontSize: 10, fontWeight: '700', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6, overflow: 'hidden' },
  school: { fontSize: 15, fontWeight: '700', color: colors.text, marginTop: 6 },
  metaRow: { flexDirection: 'row', alignItems: 'center', marginTop: 6, gap: 4, flexWrap: 'wrap' },
  meta: { color: colors.textMuted, fontSize: 12 },
  dot: { color: colors.textMuted, marginHorizontal: 4 },
  footerRow: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 10, paddingTop: 10, borderTopWidth: 1, borderTopColor: '#F1F5F9' },
  value: { color: colors.success, fontWeight: '800' },
  owner: { color: colors.textMuted, fontSize: 11 },
});
