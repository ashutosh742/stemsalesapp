// PlanningGradeTile.js
// Home-screen tile that surfaces today's planning grade. Tap to open the
// full PlanningGradeScreen. Lives at top of DayPlanScreen and PipelineScreen.
//
// Renders today's grade letter, points, hours_ahead, and a CTA to plan
// tomorrow before 18:00 IST.

import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { apiGet } from '../api/client';

const GRADE_COLOR = {
  'A+': '#1f8b3a', 'A': '#42a161', 'B': '#c9a227',
  'C': '#d97706', 'D': '#c0382b',
};

export default function PlanningGradeTile() {
  const nav = useNavigation();
  const [grade, setGrade] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const g = await apiGet('/api/planning/grade/today');
        setGrade(g);
      } catch (e) {
        // silently fail - tile just hides
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  if (loading || !grade) return null;

  const isCutoffPassed = new Date().getHours() >= 18;
  const ctaColor = isCutoffPassed ? '#c0382b' : '#1f8b3a';

  return (
    <TouchableOpacity
      onPress={() => nav.navigate('PlanningGrade')}
      style={[styles.tile, { borderLeftColor: GRADE_COLOR[grade.grade] || '#999' }]}
    >
      <View style={styles.row}>
        {grade.grade ? (
          <>
            <Text style={[styles.letter, { color: GRADE_COLOR[grade.grade] }]}>
              {grade.grade}
            </Text>
            <View style={styles.meta}>
              <Text style={styles.title}>Today's planning grade</Text>
              <Text style={styles.sub}>
                {grade.points} pts -- {grade.hours_ahead} hrs ahead
              </Text>
              {grade.idle_flag === 1 && (
                <Text style={styles.warn}>
                  3-hour-waste warning: {grade.idle_morning_minutes} idle min
                </Text>
              )}
            </View>
          </>
        ) : (
          <View style={styles.meta}>
            <Text style={styles.title}>No plan on file</Text>
            <Text style={styles.sub}>Tap to plan tomorrow now</Text>
          </View>
        )}
      </View>
      <View style={[styles.cta, { backgroundColor: ctaColor }]}>
        <Text style={styles.ctaText}>
          {isCutoffPassed ? 'Late - plan now to recover' : 'Plan tomorrow (locks A+ before 18:00 IST)'}
        </Text>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  tile: {
    backgroundColor: '#fff', marginHorizontal: 16, marginBottom: 12,
    padding: 12, borderRadius: 10, borderLeftWidth: 5,
    shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 4, elevation: 2,
  },
  row: { flexDirection: 'row', alignItems: 'center' },
  letter: { fontSize: 40, fontWeight: '800', width: 60 },
  meta: { flex: 1 },
  title: { fontWeight: '700', color: '#1f2d3d' },
  sub: { color: '#4a5568', marginTop: 2, fontSize: 13 },
  warn: { color: '#92400e', marginTop: 4, fontSize: 12 },
  cta: { marginTop: 10, padding: 8, borderRadius: 6 },
  ctaText: { color: '#fff', textAlign: 'center', fontWeight: '600', fontSize: 13 },
});
