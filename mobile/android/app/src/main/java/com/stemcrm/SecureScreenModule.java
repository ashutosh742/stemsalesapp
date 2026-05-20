// SecureScreenModule.java
// ---------------------------------------------------------------------
// Android native bridge that toggles WindowManager.LayoutParams.FLAG_SECURE
// on the host Activity. While ON, the OS blocks:
//   - Screenshots (PrintScreen returns "Couldn't capture screenshot")
//   - Screen recording (frame appears blank)
//   - Casting / screen mirroring to a second display
//   - Visible window contents in the recent-apps thumbnail
//
// The mobile app calls NativeModules.SecureScreen.setSecure(true) when
// SecureContactCard mounts and setSecure(false) when it unmounts.
package com.stemcrm;

import android.app.Activity;
import android.view.WindowManager;

import androidx.annotation.NonNull;

import com.facebook.react.bridge.ReactApplicationContext;
import com.facebook.react.bridge.ReactContextBaseJavaModule;
import com.facebook.react.bridge.ReactMethod;
import com.facebook.react.bridge.Promise;

public class SecureScreenModule extends ReactContextBaseJavaModule {

    public SecureScreenModule(@NonNull ReactApplicationContext reactContext) {
        super(reactContext);
    }

    @NonNull
    @Override
    public String getName() {
        return "SecureScreen";
    }

    @ReactMethod
    public void setSecure(final boolean enable, final Promise promise) {
        final Activity activity = getCurrentActivity();
        if (activity == null) {
            promise.reject("no_activity", "Current activity is null");
            return;
        }
        activity.runOnUiThread(new Runnable() {
            @Override
            public void run() {
                try {
                    if (enable) {
                        activity.getWindow().setFlags(
                                WindowManager.LayoutParams.FLAG_SECURE,
                                WindowManager.LayoutParams.FLAG_SECURE);
                    } else {
                        activity.getWindow().clearFlags(WindowManager.LayoutParams.FLAG_SECURE);
                    }
                    promise.resolve(enable);
                } catch (Exception e) {
                    promise.reject("flag_secure_error", e.getMessage(), e);
                }
            }
        });
    }
}
