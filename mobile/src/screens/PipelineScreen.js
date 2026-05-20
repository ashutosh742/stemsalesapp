// PipelineScreen — sales deal stages, kanban-style horizontal scroll.
// Each column is a stage; cards show lead, school (as a sub-line), value, days-in-stage.

import React, { useMemo } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, Platform,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { leads, stageColors } from '../data/mock';

const STAGES = ['New', 'Site Visit', 'Proposal Sent', 'Negotiation', 'Won'];

function parseValue(v) {
  // "₹4.2L" → 4.2
  const m = String(v).match(/([\d.]+)/);
  return m ? parseFloat(m[1]) : 0;
}

export default function PipelineScreen() {
  const byStage = useMemo(() => {
    const out = {};
    STAGES.forEach((s) => { out[s] = leads.filter((l) => l.stage === s); });
    return out;
  }, []);

  const totalInr = leads.reduce((s, l) => s + parseValue(l.value), 0);
  const openInr = leads.filter((l) => l.stage !== 'Won' && l.stage !== 'Lost')
    .reduce((s, l) => s + parseValue(l.value), 0);
  const closedInr = leads.filter((l) => l.stage === 'Won')
    .reduce((s, l) => s + parseValue(l.value), 0);

  return (
    <View style={styles.root}>
      <StatusBar barStyle="light-content" />

      {/* Header */}
      <LinearGradient
        colors={[colors.spaceTop, colors.spaceBottom]}
        start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
        style={styles.header}
      >
        <Text style={styles.headerKicker}>Pipeline</Text>
        <Text style={styles.headerTitle}>₹{totalInr.toFixed(1)}L total</Text>
        <View style={styles.headerRow}>
          <View style={styles.headerStat}>
            <Text style={styles.headerStatValue}>₹{openInr.toFixed(1)}L</Text>
            <Text style={styles.headerStatLabel}>Open</Text>
          </View>
          <View style={styles.headerDivider} />
          <View style={styles.headerStat}>
            <Text style={[styles.headerStatValue, { color: colors.success }]}>₹{closedInr.toFixed(1)}L</Text>
            <Text style={styles.headerStatLabel}>Won</Text>
          </View>
          <View style={styles.headerDivider} />
          <View style={styles.headerStat}>
            <Text style={styles.headerStatValue}>{leads.length}</Text>
            <Text style={styles.headerStatLabel}>Deals</Text>
          </View>
        </View>
      </LinearGradient>

      {/* Kanban */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.kanban}
      >
        {STAGES.map((stage) => {
          const items = byStage[stage] || [];
          const sum = items.reduce((s, l) => s + parseValue(l.value), 0);
          return (
            <View key={stage} style={styles.column}>
              <View style={styles.columnHead}>
                <View style={styles.columnHeadLeft}>
                  <View style={[styles.stageDot, { backgroundColor: stageColors[stage] || colors.textMuted }]} />
                  <Text style={styles.columnTitle}>{stage}</Text>
                </View>
                <Text style={styles.columnCount}>{items.length}</Text>
              </View>
              <Text style={styles.columnSum}>₹{sum.toFixed(1)}L</Text>

              <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ gap: 8, paddingBottom: 16 }}>
                {items.length === 0 && (
                  <View style={styles.emptyCol}>
                    <Text style={styles.emptyText}>No deals</Text>
                  </View>
                )}
                {items.map((l) => (
                  <Pressable key={l.id} style={styles.dealCard}>
                    <View style={styles.dealTop}>
                      <Text style={styles.dealId}>{l.id}</Text>
                      <Text style={styles.dealValue}>{l.value}</Text>
                    </View>
                    <Text style={styles.dealSchool} numberOfLines={2}>{l.school}</Text>
                    <Text style={styles.dealProgram} numberOfLines={1}>{l.program}</Text>
                    <View style={styles.dealMeta}>
                      <Ionicons name="location" size={10} color={colors.textMuted} />
                      <Text style={styles.dealMetaText}>{l.city}</Text>
                      <Text style={styles.dealMetaDot}>·</Text>
                      <Ionicons name="time" size={10} color={colors.textMuted} />
                      <Text style={styles.dealMetaText}>{l.updated}</Text>
                    </View>
                    <View style={styles.dealOwnerRow}>
                      <View style={styles.dealOwnerDot}><Text style={styles.dealOwnerInit}>{l.owner[0]}</Text></View>
                      <Text style={styles.dealOwner}>{l.owner}</Text>
                    </View>
                  </Pressable>
                ))}
              </ScrollView>
            </View>
          );
        })}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },
  header: {
    paddingTop: Platform.OS === 'ios' ? 58 : 38,
    paddingHorizontal: 20, paddingBottom: 18,
    borderBottomLeftRadius: 24, borderBottomRightRadius: 24,
  },
  headerKicker: { color: 'rgba(255,255,255,0.7)', fontSize: 12, fontWeight: '600', textTransform: 'uppercase', letterSpacing: 0.6 },
  headerTitle: { color: '#fff', fontSize: 26, fontWeight: '800', marginTop: 4 },
  headerRow: { flexDirection: 'row', alignItems: 'center', gap: 12, marginTop: 14 },
  headerStat: { alignItems: 'flex-start' },
  headerStatValue: { color: '#fff', fontWeight: '700', fontSize: 15 },
  headerStatLabel: { color: 'rgba(255,255,255,0.65)', fontSize: 10.5, marginTop: 2 },
  headerDivider: { width: 1, height: 24, backgroundColor: 'rgba(255,255,255,0.18)' },

  kanban: { padding: 14, gap: 10 },
  column: {
    width: 240, backgroundColor: colors.card,
    borderRadius: 14, padding: 12,
    borderWidth: 1, borderColor: colors.border,
  },
  columnHead: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  columnHeadLeft: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  stageDot: { width: 8, height: 8, borderRadius: 4 },
  columnTitle: { color: colors.text, fontWeight: '700', fontSize: 13.5 },
  columnCount: {
    color: colors.textMuted, fontSize: 11, fontWeight: '700',
    backgroundColor: colors.cardAlt, paddingHorizontal: 7, paddingVertical: 2, borderRadius: 8,
  },
  columnSum: { color: colors.textMuted, fontSize: 11, marginTop: 2, marginBottom: 10, fontWeight: '600' },

  dealCard: {
    backgroundColor: colors.cardAlt,
    borderRadius: 10, padding: 10,
    borderWidth: 1, borderColor: colors.border,
  },
  dealTop: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  dealId: { color: colors.textMuted, fontSize: 10.5, fontWeight: '700', letterSpacing: 0.3 },
  dealValue: { color: colors.btnFrom, fontSize: 13, fontWeight: '800' },
  dealSchool: { color: colors.text, fontSize: 13, fontWeight: '600', marginTop: 6, lineHeight: 17 },
  dealProgram: { color: colors.textMuted, fontSize: 11.5, marginTop: 3 },
  dealMeta: { flexDirection: 'row', alignItems: 'center', gap: 3, marginTop: 8 },
  dealMetaText: { color: colors.textMuted, fontSize: 10.5 },
  dealMetaDot: { color: colors.textMuted, fontSize: 10.5, marginHorizontal: 2 },
  dealOwnerRow: { flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: 8 },
  dealOwnerDot: {
    width: 18, height: 18, borderRadius: 9,
    backgroundColor: colors.btnFrom,
    alignItems: 'center', justifyContent: 'center',
  },
  dealOwnerInit: { color: '#fff', fontSize: 10, fontWeight: '700' },
  dealOwner: { color: colors.textMuted, fontSize: 11, fontWeight: '500' },

  emptyCol: {
    paddingVertical: 24, alignItems: 'center',
    borderWidth: 1, borderStyle: 'dashed', borderColor: colors.border, borderRadius: 10,
  },
  emptyText: { color: colors.textMuted, fontSize: 11 },
});
