import React, { useState, useEffect, useRef } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Animated, Easing, KeyboardAvoidingView, Platform,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';

function Star({ x, y, size, delay }) {
  const op = useRef(new Animated.Value(0.2)).current;
  useEffect(() => {
    Animated.loop(
      Animated.sequence([
        Animated.timing(op, { toValue: 1, duration: 1200 + delay, useNativeDriver: true }),
        Animated.timing(op, { toValue: 0.2, duration: 1200 + delay, useNativeDriver: true }),
      ])
    ).start();
  }, []);
  return (
    <Animated.View style={{
      position: 'absolute', left: x, top: y, width: size, height: size,
      borderRadius: size / 2, backgroundColor: '#fff', opacity: op,
    }} />
  );
}

const STARS = Array.from({ length: 60 }, (_, i) => ({
  x: Math.random() * 400,
  y: Math.random() * 800,
  size: Math.random() * 2 + 1,
  delay: Math.random() * 1500,
  id: i,
}));

export default function LoginScreen({ onLogin }) {
  const [userId, setUserId] = useState('field.officer');
  const [password, setPassword] = useState('demo1234');
  const [showPw, setShowPw] = useState(false);

  // Animated border hue
  const hue = useRef(new Animated.Value(0)).current;
  useEffect(() => {
    Animated.loop(
      Animated.timing(hue, { toValue: 1, duration: 4000, easing: Easing.linear, useNativeDriver: false })
    ).start();
  }, []);
  const borderColor = hue.interpolate({
    inputRange: [0, 0.2, 0.4, 0.6, 0.8, 1],
    outputRange: ['#3498db', '#9b59b6', '#e74c3c', '#f39c12', '#2ecc71', '#3498db'],
  });

  return (
    <View style={styles.root}>
      <LinearGradient colors={[colors.spaceTop, colors.spaceBottom]} style={StyleSheet.absoluteFill} />
      {STARS.map(s => <Star key={s.id} {...s} />)}

      {/* Planets */}
      <View style={[styles.planet, { backgroundColor: '#FFB347', top: 60, right: 30, width: 70, height: 70, shadowColor: '#FF8A00' }]} />
      <View style={[styles.planet, { backgroundColor: '#4FC3F7', top: 140, left: 20, width: 36, height: 36, shadowColor: '#1E90FF' }]} />
      <View style={[styles.planet, { backgroundColor: '#E57373', bottom: 120, right: 50, width: 28, height: 28, shadowColor: '#E74C3C' }]} />

      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={styles.center}>
        <Animated.View style={[styles.card, { borderColor }]}>
          {/* Logo */}
          <View style={styles.logoRow}>
            <View style={styles.logoBadge}>
              <Text style={styles.logoBadgeText}>S</Text>
            </View>
            <View>
              <Text style={styles.logoTitle}>STEM</Text>
              <Text style={styles.logoSub}>Learning</Text>
            </View>
          </View>
          <Text style={styles.tagline}>Building Brains... Beyond Books...</Text>

          <Text style={styles.crmTitle}>CRMApp</Text>
          <Text style={styles.signinSub}>Sign in to start your session</Text>

          <View style={styles.field}>
            <Ionicons name="person-outline" size={18} color={colors.textMuted} style={styles.fieldIcon} />
            <TextInput
              value={userId}
              onChangeText={setUserId}
              placeholder="User ID"
              placeholderTextColor={colors.textMuted}
              autoCapitalize="none"
              style={styles.input}
            />
          </View>

          <View style={styles.field}>
            <Ionicons name="lock-closed-outline" size={18} color={colors.textMuted} style={styles.fieldIcon} />
            <TextInput
              value={password}
              onChangeText={setPassword}
              placeholder="Password"
              placeholderTextColor={colors.textMuted}
              secureTextEntry={!showPw}
              style={styles.input}
            />
            <TouchableOpacity onPress={() => setShowPw(v => !v)} style={{ padding: 6 }}>
              <Ionicons name={showPw ? 'eye-off-outline' : 'eye-outline'} size={18} color={colors.textMuted} />
            </TouchableOpacity>
          </View>

          <TouchableOpacity onPress={onLogin} activeOpacity={0.85} style={{ marginTop: 4 }}>
            <LinearGradient colors={[colors.btnFrom, colors.btnTo]} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.btn}>
              <Text style={styles.btnText}>Sign In</Text>
              <Ionicons name="arrow-forward" size={18} color="#fff" />
            </LinearGradient>
          </TouchableOpacity>

          <Text style={styles.hint}>Demo mode — tap Sign In to continue</Text>
        </Animated.View>

        <Text style={styles.footer}>STEM Learning India · v1.0 prototype</Text>
      </KeyboardAvoidingView>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.spaceBottom },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingHorizontal: 22 },
  planet: {
    position: 'absolute', borderRadius: 999,
    shadowOpacity: 0.7, shadowRadius: 18, shadowOffset: { width: 0, height: 0 }, elevation: 10,
  },
  card: {
    width: '100%', maxWidth: 380, padding: 22, borderRadius: 22, borderWidth: 2,
    backgroundColor: 'rgba(255, 248, 220, 0.96)',
  },
  logoRow: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  logoBadge: {
    width: 40, height: 40, borderRadius: 10, backgroundColor: colors.brandOrange,
    justifyContent: 'center', alignItems: 'center',
  },
  logoBadgeText: { color: '#fff', fontWeight: '800', fontSize: 22 },
  logoTitle: { fontSize: 20, fontWeight: '800', color: colors.brandOrange, letterSpacing: 1 },
  logoSub: { fontSize: 11, color: colors.brandBlue, fontWeight: '700', letterSpacing: 1, marginTop: -2 },
  tagline: { fontSize: 11, color: '#555', marginTop: 8, fontStyle: 'italic' },
  crmTitle: { fontSize: 34, fontWeight: '900', color: colors.brandPink, marginTop: 16, letterSpacing: 0.5 },
  signinSub: { color: '#666', marginTop: 2, marginBottom: 18 },
  field: {
    flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff',
    borderRadius: 10, borderWidth: 1, borderColor: '#E5E9F2',
    paddingHorizontal: 10, marginBottom: 10,
  },
  fieldIcon: { marginRight: 8 },
  input: { flex: 1, paddingVertical: 12, color: colors.text, fontSize: 15 },
  btn: {
    paddingVertical: 14, borderRadius: 10, flexDirection: 'row',
    justifyContent: 'center', alignItems: 'center', gap: 8, marginTop: 6,
  },
  btnText: { color: '#fff', fontWeight: '800', fontSize: 16, letterSpacing: 0.5 },
  hint: { textAlign: 'center', color: '#888', fontSize: 11, marginTop: 12 },
  footer: { color: '#9CA3AF', fontSize: 11, marginTop: 20 },
});
